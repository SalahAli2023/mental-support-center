<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientSession;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PatientSessionController extends Controller
{
    /**
     * عرض جميع جلسات المريض
     */
    public function index(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $query = $patient->sessions()->with(['therapist', 'patient.user']);

        // التصفية بالحالة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // التصفية بالنوع
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // التصفية بالموقع
        if ($request->has('location') && $request->location) {
            $query->where('location', $request->location);
        }

        // التصفية بالتاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->where('session_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('session_date', '<=', $request->date_to);
        }

        // التصفية بالمعالج
        if ($request->has('therapist_id') && $request->therapist_id) {
            $query->where('therapist_id', $request->therapist_id);
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'session_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query->orderBy($sortBy, $sortOrder)
              ->orderBy('session_time', $sortOrder);

        // التقسيم الصفحي
        $perPage = $request->get('per_page', 10);
        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'sessions' => $sessions->items(),
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'total_pages' => $sessions->lastPage(),
                'total_items' => $sessions->total(),
                'per_page' => $sessions->perPage(),
            ]
        ]);
    }

    /**
     * عرض جلسة محددة
     */
    public function show($patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->with(['patient.user', 'therapist'])
            ->findOrFail($sessionId);

        return response()->json([
            'success' => true,
            'session' => $this->formatSessionData($session)
        ]);
    }

    /**
     * إضافة جلسة جديدة
     */
    public function store(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_time' => 'required|date_format:H:i',
            'therapist_id' => 'required|exists:users,id',
            'status' => ['required', Rule::in(['scheduled', 'completed', 'cancelled'])],
            'progress' => 'nullable|integer|min:0|max:100',
            'type' => ['required', Rule::in(['individual', 'group', 'family', 'assessment', 'followup'])],
            'location' => ['required', Rule::in(['clinic', 'online', 'home'])],
            'notes_ar' => 'nullable|string',
            'notes_en' => 'nullable|string',
            'report_ar' => 'nullable|string',
            'report_en' => 'nullable|string',
            'duration' => 'nullable|integer|min:15|max:240', // بالدقائق
        ]);

        // التحقق من توفر الموعد
        $isTimeSlotAvailable = $this->checkTimeSlotAvailability(
            $validated['therapist_id'],
            $validated['session_date'],
            $validated['session_time'],
            null,
            $validated['duration'] ?? 60
        );

        if (!$isTimeSlotAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الموعد غير متاح للمعالج المحدد'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $session = $patient->sessions()->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الجلسة بنجاح',
                'session' => $this->formatSessionData($session->load(['therapist', 'patient.user']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الجلسة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث جلسة
     */
    public function update(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'session_date' => 'sometimes|required|date',
            'session_time' => 'sometimes|required|date_format:H:i',
            'therapist_id' => 'sometimes|required|exists:users,id',
            'status' => ['sometimes', 'required', Rule::in(['scheduled', 'completed', 'cancelled'])],
            'progress' => 'nullable|integer|min:0|max:100',
            'type' => ['sometimes', 'required', Rule::in(['individual', 'group', 'family', 'assessment', 'followup'])],
            'location' => ['sometimes', 'required', Rule::in(['clinic', 'online', 'home'])],
            'notes_ar' => 'nullable|string',
            'notes_en' => 'nullable|string',
            'report_ar' => 'nullable|string',
            'report_en' => 'nullable|string',
            'duration' => 'nullable|integer|min:15|max:240',
        ]);

        // التحقق من توفر الموعد (إذا تم تغيير الوقت أو المعالج)
        if (isset($validated['therapist_id']) || isset($validated['session_date']) || isset($validated['session_time'])) {
            $therapistId = $validated['therapist_id'] ?? $session->therapist_id;
            $sessionDate = $validated['session_date'] ?? $session->session_date;
            $sessionTime = $validated['session_time'] ?? $session->session_time;
            $duration = $validated['duration'] ?? $session->duration ?? 60;

            $isTimeSlotAvailable = $this->checkTimeSlotAvailability(
                $therapistId,
                $sessionDate,
                $sessionTime,
                $sessionId,
                $duration
            );

            if (!$isTimeSlotAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الموعد غير متاح للمعالج المحدد'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $session->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الجلسة بنجاح',
                'session' => $this->formatSessionData($session->fresh(['therapist', 'patient.user']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الجلسة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف جلسة
     */
    public function destroy($patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        DB::beginTransaction();
        try {
            // حذف المرفقات أولاً
            if ($session->attachments) {
                foreach ($session->attachments as $attachment) {
                    if (Storage::exists($attachment['path'])) {
                        Storage::delete($attachment['path']);
                    }
                }
            }

            $session->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الجلسة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الجلسة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة الجلسة
     */
    public function updateStatus(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['scheduled', 'completed', 'cancelled'])]
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الجلسة بنجاح',
            'session' => $this->formatSessionData($session->fresh(['therapist', 'patient.user']))
        ]);
    }

    /**
     * تحديث تقدم الجلسة
     */
    public function updateProgress(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث تقدم الجلسة بنجاح',
            'session' => $this->formatSessionData($session->fresh(['therapist', 'patient.user']))
        ]);
    }

    /**
     * إضافة ملاحظات للجلسة
     */
    public function addNotes(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'notes_ar' => 'required|string',
            'notes_en' => 'required|string'
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الملاحظات بنجاح',
            'session' => $this->formatSessionData($session->fresh(['therapist', 'patient.user']))
        ]);
    }

    /**
     * إضافة تقرير للجلسة
     */
    public function addReport(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'report_ar' => 'required|string',
            'report_en' => 'required|string'
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التقرير بنجاح',
            'session' => $this->formatSessionData($session->fresh(['therapist', 'patient.user']))
        ]);
    }

    /**
     * رفع مرفقات للجلسة
     */
    public function uploadAttachments(Request $request, $patientId, $sessionId)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $request->validate([
            'attachments.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB
        ]);

        $uploadedAttachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('patient-sessions/attachments');
                
                $uploadedAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toDateTimeString()
                ];
            }

            // دمج المرفقات الجديدة مع القديمة
            $currentAttachments = $session->attachments ?? [];
            $allAttachments = array_merge($currentAttachments, $uploadedAttachments);

            $session->update(['attachments' => $allAttachments]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم رفع المرفقات بنجاح',
            'attachments' => $uploadedAttachments,
            'session' => $this->formatSessionData($session->fresh())
        ]);
    }

    /**
     * حذف مرفق من الجلسة
     */
    public function deleteAttachment($patientId, $sessionId, $attachmentIndex)
    {
        $session = PatientSession::where('patient_id', $patientId)
            ->findOrFail($sessionId);

        $attachments = $session->attachments ?? [];

        if (!isset($attachments[$attachmentIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'المرفق غير موجود'
            ], 404);
        }

        $attachmentToDelete = $attachments[$attachmentIndex];

        // حذف الملف من التخزين
        if (Storage::exists($attachmentToDelete['path'])) {
            Storage::delete($attachmentToDelete['path']);
        }

        // إزالة المرفق من المصفوفة
        unset($attachments[$attachmentIndex]);
        $attachments = array_values($attachments); // إعادة ترتيب الفهرس

        $session->update(['attachments' => $attachments]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المرفق بنجاح',
            'session' => $this->formatSessionData($session->fresh())
        ]);
    }

    /**
     * الحصول على إحصائيات الجلسات
     */
    public function getStats($patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);

        $patientSessionQuery = $patient->sessions();

        $stats = [
            'total_sessions' => $patientSessionQuery->count(),
            'completed_sessions' => (clone $patientSessionQuery)->where('status', 'completed')->count(),
            'scheduled_sessions' => (clone $patientSessionQuery)->where('status', 'scheduled')->count(),
            'cancelled_sessions' => (clone $patientSessionQuery)->where('status', 'cancelled')->count(),
            'average_progress' => round((clone $patientSessionQuery)->where('status', 'completed')->avg('progress') ?? 0),
            'total_duration' => $patient->sessions()->sum('duration') ?? 0,
            'upcoming_session' => (clone $patientSessionQuery)
                ->where('status', 'scheduled')
                ->where('session_date', '>=', now())
                ->orderBy('session_date')
                ->orderBy('session_time')
                ->first(),
            'last_session' => (clone $patientSessionQuery)
                ->where('status', 'completed')
                ->orderBy('session_date', 'desc')
                ->orderBy('session_time', 'desc')
                ->first(),
            'sessions_by_type' => $patient->sessions()
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type'),
            'sessions_by_location' => $patient->sessions()
                ->select('location', DB::raw('count(*) as count'))
                ->groupBy('location')
                ->get()
                ->pluck('count', 'location'),
        ];

        // Video session metrics (from video_sessions table)
        $videoQuery = Session::with('appointment')
            ->whereHas('appointment', function ($q) use ($patient) {
                $q->where('client_id', $patient->user_id);
            });

        $videoStats = [
            'total_sessions' => (clone $videoQuery)->count(),
            'completed_sessions' => (clone $videoQuery)->where('status', 'ended')->count(),
            'scheduled_sessions' => (clone $videoQuery)->whereIn('status', ['scheduled', 'active'])->count(),
            'cancelled_sessions' => (clone $videoQuery)->where('status', 'cancelled')->count(),
            'average_progress' => round((clone $videoQuery)->whereNotNull('progress_score')->avg('progress_score') ?? 0),
        ];

        $lastVideoSession = (clone $videoQuery)
            ->whereNotNull('start_time')
            ->orderByDesc('start_time')
            ->first();

        $videoStats['last_session'] = $lastVideoSession ? [
            'id' => $lastVideoSession->id,
            'date' => optional($lastVideoSession->end_time ?? $lastVideoSession->start_time)?->toDateTimeString(),
            'status' => $lastVideoSession->status,
        ] : null;

        $nextVideoSession = (clone $videoQuery)
            ->whereIn('status', ['scheduled', 'active'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->first();

        $videoStats['next_session'] = $nextVideoSession ? [
            'id' => $nextVideoSession->id,
            'date' => optional($nextVideoSession->start_time)?->toDateTimeString(),
            'status' => $nextVideoSession->status,
        ] : null;

        // If no clinical sessions exist, fallback to video stats for aggregate numbers
        if ($stats['total_sessions'] === 0 && $videoStats['total_sessions'] > 0) {
            $stats['total_sessions'] = $videoStats['total_sessions'];
            $stats['completed_sessions'] = $videoStats['completed_sessions'];
            $stats['scheduled_sessions'] = $videoStats['scheduled_sessions'];
            $stats['cancelled_sessions'] = $videoStats['cancelled_sessions'];
            $stats['average_progress'] = $videoStats['average_progress'];
            $stats['upcoming_session'] = $videoStats['next_session'];
            $stats['last_session'] = $videoStats['last_session'];
        }

        $stats['video_sessions'] = $videoStats;

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * الحصول على المواعيد المتاحة للمعالج
     */
    public function getAvailableSlots(Request $request, $patientId)
    {
        $request->validate([
            'therapist_id' => 'required|exists:users,id',
            'session_date' => 'required|date',
            'duration' => 'nullable|integer|min:15|max:240'
        ]);

        $therapistId = $request->therapist_id;
        $sessionDate = $request->session_date;
        $duration = $request->duration ?? 60;

        $availableSlots = $this->generateAvailableSlots($therapistId, $sessionDate, $duration);

        return response()->json([
            'success' => true,
            'available_slots' => $availableSlots
        ]);
    }

    /**
     * التحقق من توفر الموعد
     */
    private function checkTimeSlotAvailability($therapistId, $sessionDate, $sessionTime, $excludeSessionId = null, $duration = 60)
    {
        $startTime = $sessionTime;
        $endTime = date('H:i', strtotime("+{$duration} minutes", strtotime($sessionTime)));

        $query = PatientSession::where('therapist_id', $therapistId)
            ->where('session_date', $sessionDate)
            ->where(function($q) use ($startTime, $endTime) {
                $q->whereBetween('session_time', [$startTime, $endTime])
                  ->orWhere(function($q) use ($startTime, $endTime) {
                      $q->where('session_time', '<', $endTime)
                        ->whereRaw("ADDTIME(session_time, '00:60:00') > ?", [$startTime]);
                  });
            })
            ->whereIn('status', ['scheduled', 'completed']);

        if ($excludeSessionId) {
            $query->where('id', '!=', $excludeSessionId);
        }

        return !$query->exists();
    }

    /**
     * توليد المواعيد المتاحة
     */
    private function generateAvailableSlots($therapistId, $sessionDate, $duration = 60)
    {
        // الأوقات المتاحة الافتراضية
        $timeSlots = [
            '08:00', '09:00', '10:00', '11:00', '12:00', 
            '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'
        ];

        $availableSlots = [];

        foreach ($timeSlots as $slot) {
            $isAvailable = $this->checkTimeSlotAvailability($therapistId, $sessionDate, $slot, null, $duration);
            
            $availableSlots[] = [
                'time' => $slot,
                'available' => $isAvailable,
                'label' => $slot,
                'duration' => $duration
            ];
        }

        return $availableSlots;
    }

    /**
     * تنسيق بيانات الجلسة للـ API
     */
    private function formatSessionData($session)
    {
        return [
            'id' => $session->id,
            'title' => $session->title,
            'title_ar' => $session->title_ar,
            'title_en' => $session->title_en,
            'session_date' => $session->session_date->format('Y-m-d'),
            'session_time' => $session->session_time,
            'status' => $session->status,
            'progress' => $session->progress,
            'type' => $session->type,
            'location' => $session->location,
            'notes' => $session->notes,
            'notes_ar' => $session->notes_ar,
            'notes_en' => $session->notes_en,
            'report' => $session->report,
            'report_ar' => $session->report_ar,
            'report_en' => $session->report_en,
            'duration' => $session->duration,
            'attachments' => $session->attachments ?? [],
            'created_at' => $session->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $session->updated_at->format('Y-m-d H:i:s'),
            'patient' => $session->patient ? [
                'id' => $session->patient->id,
                'name' => $session->patient->user->name,
                'email' => $session->patient->user->email,
                'phone' => $session->patient->user->phone
            ] : null,
            'therapist' => $session->therapist ? [
            'id' => $session->therapist->id,
            'name' => $session->therapist->name_ar ?? $session->therapist->name_en,
            'specialty' => $session->therapist->specialty_ar ?? $session->therapist->specialty_en,
            'session_duration' => $session->therapist->session_duration
            ] : null
        ];
    }
}