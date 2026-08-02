<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Appointment::with(['client', 'therapist.user']);

        // Role-based filtering
        if ($user && $user->isClient()) {
            $query->where('client_id', $user->id);
        } elseif ($user && $user->isTherapist() && $user->therapist) {
            $query->where('therapist_id', $user->therapist->id);
        }
        // Admin sees all appointments - no filter needed

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('therapist_id')) {
            $query->where('therapist_id', $request->therapist_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->where('starts_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('starts_at', '<=', $request->to_date);
        }

        $appointments = $query->orderBy('starts_at', 'desc')->paginate($request->get('per_page', 15));

        return AppointmentResource::collection($appointments)->response();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        // يجب أن يكون المستخدم مسجلاً للدخول ليحجز جلسة
        if (!$user) {
            return response()->json([
                'message' => 'يجب تسجيل الدخول قبل حجز الجلسة.',
            ], 401);
        }

        $rules = [
            'therapist_id' => 'required|exists:therapists,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'nullable|date|after:starts_at',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
        ];

        // إذا كان الادمن، يسمح له بتحديد client_id
        if ($user->isAdmin()) {
            $rules['client_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // إذا كان الادمن، استخدم client_id من الطلب، وإلا استخدم المستخدم الحالي
        $clientId = $user->isAdmin() && isset($validated['client_id']) 
            ? $validated['client_id'] 
            : $user->id;

        $appointment = Appointment::create([
            'client_id' => $clientId,
            'therapist_id' => $validated['therapist_id'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? \Carbon\Carbon::parse($validated['starts_at'])->addHour(),
            'status' => $validated['status'] ?? 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // عند حجز جلسة لأول مرة: التأكد من وجود سجل مريض مرتبط بهذا العميل
        if (!Patient::where('user_id', $clientId)->exists()) {
            Patient::create([
                'user_id' => $clientId,
                'status' => 'active',
            ]);
        }

        // إنشاء جلسة تلقائياً عند إنشاء الموعد (إذا كان الموعد مؤكد أو تم إنشاؤه من لوحة التحكم)
        // إذا كان الادمن أنشأ الموعد مباشرة بحالة confirmed، أو إذا كان الموعد بحالة confirmed
        $appointmentStatus = $validated['status'] ?? 'pending';
        if ($appointmentStatus === 'confirmed' || ($user->isAdmin() && $appointmentStatus !== 'cancelled')) {
            try {
                // التحقق من عدم وجود جلسة موجودة بالفعل
                $existingSession = \App\Models\Session::where('appointment_id', $appointment->id)
                    ->whereIn('status', ['scheduled', 'active'])
                    ->first();

                if (!$existingSession) {
                    \App\Models\Session::create([
                        'appointment_id' => $appointment->id,
                        'room_id' => \App\Models\Session::generateRoomId(),
                        'start_time' => $appointment->starts_at,
                        'status' => 'scheduled',
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                // لا نوقف العملية إذا فشل إنشاء الجلسة، فقط نسجل الخطأ
                \Log::warning('Failed to auto-create session for appointment: ' . $appointment->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return (new AppointmentResource($appointment->load(['client', 'therapist'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $appointment = Appointment::with(['client', 'therapist'])->findOrFail($id);
        $user = $request->user();

        // Only admin or the appointment's client/therapist can view
        if ($user && !$user->isAdmin()) {
            $isClientOwner = $user->isClient() && $appointment->client_id === $user->id;
            $isTherapistOwner = $user->isTherapist() && $user->therapist && $appointment->therapist_id === $user->therapist->id;

            if (!$isClientOwner && !$isTherapistOwner) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return new AppointmentResource($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = $request->user();

        // Only admin or the appointment's client/therapist can update
        if (!$user->isAdmin() && 
            $appointment->client_id !== $user->id && 
            ($user->therapist && $appointment->therapist_id !== $user->therapist->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'starts_at' => 'sometimes|date|after:now',
            'ends_at' => 'sometimes|date|after:starts_at',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'cancellation_reason' => 'nullable|string|max:500|required_if:status,cancelled',
        ]);

        // Status flow validation
        // للأدمن: السماح بتغيير الحالة من أي حالة إلى أي حالة (مرونة في الإدارة)
        // للمستخدمين العاديين: تطبيق قواعد الانتقال العادية
        if ($request->has('status') && $request->status !== $appointment->status) {
            if (!$user->isAdmin()) {
                // قواعد الانتقال للمستخدمين العاديين
                $validStatusTransitions = [
                    'pending' => ['confirmed', 'cancelled'],
                    'confirmed' => ['completed', 'cancelled'],
                    'completed' => [], // Terminal state
                    'cancelled' => [], // Terminal state
                ];

                if (!in_array($request->status, $validStatusTransitions[$appointment->status] ?? [])) {
                    return response()->json([
                        'message' => 'Invalid status transition',
                        'current_status' => $appointment->status,
                        'allowed_transitions' => $validStatusTransitions[$appointment->status] ?? []
                    ], 422);
                }
            }
            // للأدمن: السماح بأي تغيير في الحالة (لا حاجة للتحقق)
        }

        $appointment->update($request->all());

        // إنشاء جلسة تلقائياً عند تغيير حالة الموعد إلى confirmed
        if ($request->has('status') && $request->status === 'confirmed') {
            try {
                // التحقق من عدم وجود جلسة موجودة بالفعل
                $existingSession = \App\Models\Session::where('appointment_id', $appointment->id)
                    ->whereIn('status', ['scheduled', 'active'])
                    ->first();

                if (!$existingSession) {
                    \App\Models\Session::create([
                        'appointment_id' => $appointment->id,
                        'room_id' => \App\Models\Session::generateRoomId(),
                        'start_time' => $appointment->starts_at,
                        'status' => 'scheduled',
                        'notes' => $appointment->notes,
                    ]);
                }
            } catch (\Exception $e) {
                // لا نوقف العملية إذا فشل إنشاء الجلسة، فقط نسجل الخطأ
                \Log::warning('Failed to auto-create session for appointment: ' . $appointment->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return new AppointmentResource($appointment->load(['client', 'therapist']));
    }

    public function destroy(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = $request->user();

        // Only admin or the appointment's client can delete
        if (!$user->isAdmin() && $appointment->client_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent deletion of completed appointments
        if ($appointment->status === 'completed') {
            return response()->json(['message' => 'Cannot delete completed appointments'], 422);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
