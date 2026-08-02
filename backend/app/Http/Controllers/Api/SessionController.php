<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SessionResource;
use App\Http\Resources\TherapistReviewResource;
use App\Models\Session;
use App\Models\Appointment;
use App\Models\TherapistReview;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SessionController extends Controller
{
    /**
     * List all sessions (with role-based filtering)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Role-based filtering أولاً قبل eager loading
        if ($user->isClient()) {
            // استخدام whereIn مع subquery لضمان العمل مع SQLite
            $appointmentIds = \DB::table('appointments')
                ->where('client_id', $user->id)
                ->pluck('id')
                ->toArray();
            
            if (empty($appointmentIds)) {
                // لا توجد مواعيد للعميل، لا نعيد أي جلسات
                $query = Session::whereRaw('1 = 0');
            } else {
                $query = Session::whereIn('appointment_id', $appointmentIds);
            }
        } elseif ($user->isTherapist()) {
            // تحميل علاقة therapist إذا لم تكن محمّلة
            if (!$user->relationLoaded('therapist')) {
                $user->load('therapist');
            }
            
            // إذا كان للمستخدم سجل therapist، فلتر حسب therapist_id
            if ($user->therapist) {
                // استخدام whereIn مع subquery لضمان العمل مع SQLite
                $appointmentIds = \DB::table('appointments')
                    ->where('therapist_id', $user->therapist->id)
                    ->pluck('id')
                    ->toArray();
                
                if (empty($appointmentIds)) {
                    // لا توجد مواعيد للمعالج، لا نعيد أي جلسات
                    $query = Session::whereRaw('1 = 0');
                } else {
                    $query = Session::whereIn('appointment_id', $appointmentIds);
                }
            } else {
                // إذا لم يكن هناك سجل therapist، لا نعيد أي جلسات
                $query = Session::whereRaw('1 = 0');
            }
        } else {
            // Admin sees all sessions
            $query = Session::query();
        }
        
        // إضافة eager loading بعد الفلترة
        $query->with([
            'appointment.client',
            'appointment.therapist.user',
            'review.client',
        ]);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return SessionResource::collection($sessions)->response();
    }

    /**
     * Create a session from an appointment (Admin only)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Only admin can create sessions
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Only admins can create sessions.'], 403);
        }

        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot create session. Appointment was cancelled.',
            ], 422);
        }

        if ($appointment->status === 'pending') {
            $appointment->update(['status' => 'confirmed']);
        } elseif ($appointment->status === 'completed') {
            return response()->json([
                'message' => 'Appointment already completed.',
            ], 422);
        }

        // Check if session already exists for this appointment
        $existingSession = Session::where('appointment_id', $appointment->id)
            ->whereIn('status', ['scheduled', 'active'])
            ->first();

        if ($existingSession) {
            return response()->json([
                'message' => 'A session already exists for this appointment',
                'session' => new SessionResource($existingSession)
            ], 422);
        }

        $session = Session::create([
            'appointment_id' => $appointment->id,
            'room_id' => Session::generateRoomId(),
            'start_time' => $appointment->starts_at,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        // TODO: Send notifications to patient and therapist
        // Notification::send([$appointment->client, $appointment->therapist->user], new SessionCreated($session));

        return (new SessionResource($session->load(['appointment.client', 'appointment.therapist'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a specific session
     */
    public function show(Request $request, $id)
    {
        $session = Session::with(['appointment.client', 'appointment.therapist.user', 'review.client'])->findOrFail($id);
        $user = $request->user();

        // Check access
        if (!$this->canAccessSession($user, $session)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new SessionResource($session);
    }

    /**
     * Get session by room_id (for video session access)
     */
    public function getByRoomId(Request $request, $roomId)
    {
        $session = Session::with(['appointment.client', 'appointment.therapist.user', 'review.client'])
            ->where('room_id', $roomId)
            ->firstOrFail();

        $user = $request->user();

        // Admin cannot join sessions (only manage them)
        if ($user->isAdmin()) {
            return response()->json([
                'message' => 'المشرفون لا يمكنهم الانضمام إلى الجلسات. يمكنهم فقط إدارتها من لوحة التحكم.',
            ], 403);
        }

        // Check access
        if (!$this->canAccessSession($user, $session)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (in_array($session->status, ['ended', 'cancelled'])) {
            return response()->json([
                'message' => 'تم إنهاء هذه الجلسة ولا يمكن الانضمام إليها.',
                'status' => $session->status,
            ], 410);
        }

        return new SessionResource($session);
    }

    /**
     * Update session status
     */
    public function update(Request $request, $id)
    {
        $session = Session::with(['appointment.therapist'])->findOrFail($id);
        $user = $request->user();

        // Only admin, therapist, or client can update
        if (!$user->isAdmin() && !$this->canAccessSession($user, $session)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'sometimes|in:scheduled,active,ended,cancelled',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Auto-set timestamps based on status
        if ($request->has('status')) {
            if ($request->status === 'active' && !$session->start_time) {
                $request->merge(['start_time' => now()]);
            }
            if (in_array($request->status, ['ended', 'cancelled']) && !$session->end_time) {
                $request->merge(['end_time' => now()]);
            }
        }

        $session->update($request->all());

        return new SessionResource($session->load(['appointment.client', 'appointment.therapist']));
    }

    /**
     * Start a session
     */
    public function start(Request $request, $id)
    {
        $session = Session::with(['appointment.therapist.user', 'review'])->findOrFail($id);
        $user = $request->user();

        // Admin can start sessions (manage them), but regular users must have access
        if (!$user->isAdmin() && !$this->canAccessSession($user, $session)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($session->status !== 'scheduled') {
            return response()->json([
                'message' => 'Session cannot be started',
                'current_status' => $session->status
            ], 422);
        }

        $session->update([
            'status' => 'active',
            'start_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم بدء الجلسة بنجاح',
            'data' => new SessionResource($session->load(['appointment.client', 'appointment.therapist']))
        ]);
    }

    /**
     * End a session
     */
    public function end(Request $request, $id)
    {
        // تحميل العلاقة appointment أولاً
        $session = Session::with('appointment')->findOrFail($id);
        $user = $request->user();
        
        // التحقق من الصلاحيات
        $isAssignedTherapist = $user->isTherapist() 
            && $user->therapist 
            && $session->appointment 
            && $session->appointment->therapist_id === $user->therapist->id;

        if (!$user->isAdmin() && !$isAssignedTherapist) {
            return response()->json([
                'message' => 'Unauthorized. Only the assigned therapist or admin can end the session.'
            ], 403);
        }

        if ($session->status !== 'active') {
            return response()->json([
                'message' => 'Session is not active',
                'current_status' => $session->status
            ], 422);
        }

        // تحديث حالة الجلسة أولاً (قبل إرسال الإشعارات)
        $session->update([
            'status' => 'ended',
            'end_time' => now(),
        ]);

        // تحديث حالة الموعد إلى completed
        if ($session->appointment) {
            $session->appointment->update(['status' => 'completed']);
        }

        // إرسال إشعار إنهاء الجلسة لجميع المشاركين عبر Socket.IO (في الخلفية، لا ننتظر النتيجة)
        // استخدام queue أو fire-and-forget لتجنب تأخير الاستجابة
        try {
            $signalingServerUrl = env('SIGNALING_SERVER_URL', 'http://localhost:3001');
            Http::timeout(2)->post("{$signalingServerUrl}/api/session/end", [
                'roomId' => $session->room_id
            ]);
            \Log::info('Session end notification sent', [
                'room_id' => $session->room_id,
            ]);
        } catch (\Exception $e) {
            // لا نوقف العملية إذا فشل إرسال الإشعار
            \Log::warning("Failed to send session end notification", [
                'room_id' => $session->room_id,
                'error' => $e->getMessage(),
            ]);
        }

        // إعادة تحميل الجلسة بعد التحديث
        $session->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء الجلسة بنجاح',
            'data' => new SessionResource($session->load(['appointment.client', 'appointment.therapist']))
        ]);
    }

    /**
     * Record therapist progress notes for a session.
     */
    public function recordProgress(Request $request, $id)
    {
        $session = Session::with('appointment.therapist.user', 'appointment.client')->findOrFail($id);
        $user = $request->user();

        $isAssignedTherapist = $user->isTherapist()
            && $user->therapist
            && $session->appointment->therapist_id === $user->therapist->id;

        if (!$user->isAdmin() && !$isAssignedTherapist) {
            return response()->json(['message' => 'Unauthorized. Only the assigned therapist or admin can record progress.'], 403);
        }

        if ($session->status !== 'ended') {
            return response()->json([
                'message' => 'لا يمكن تسجيل التقييم إلا بعد إنهاء الجلسة.',
                'current_status' => $session->status
            ], 422);
        }

        $validated = $request->validate([
            'progress_score' => 'required|integer|min:0|max:100',
            'therapist_notes' => 'required|string|max:2000',
        ]);

        $session->update($validated);

        return new SessionResource($session->fresh()->load(['appointment.client', 'appointment.therapist']));
    }

    /**
     * Allow patients to rate their therapist after the session.
     */
    public function submitReview(Request $request, $id)
    {
        $session = Session::with(['appointment.therapist', 'review'])->findOrFail($id);
        $user = $request->user();

        if (!$user->isClient() || $session->appointment->client_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($session->status !== 'ended') {
            return response()->json([
                'message' => 'لا يمكن تقييم الجلسة قبل إنهائها.',
            ], 422);
        }

        if ($session->review) {
            return response()->json([
                'message' => 'لقد قمت بتقييم هذه الجلسة بالفعل.',
            ], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $patient = $user->patient;

        $review = TherapistReview::create([
            'session_id' => $session->id,
            'therapist_id' => $session->appointment->therapist_id,
            'client_id' => $user->id,
            'patient_id' => $patient?->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $this->recalculateTherapistRating($session->appointment->therapist);

        return response()->json([
            'message' => 'تم تسجيل تقييمك بنجاح.',
            'data' => new TherapistReviewResource($review->load('client')),
        ], 201);
    }

    /**
     * Check if user can access the session
     */
    protected function canAccessSession($user, Session $session): bool
    {
        // Admin cannot join sessions (only manage them)
        // Removed: if ($user->isAdmin()) { return true; }

        // Client can access their own sessions
        if ($user->isClient() && $session->appointment->client_id === $user->id) {
            return true;
        }

        // Therapist can access their own sessions
        if ($user->isTherapist() && $user->therapist && 
            $session->appointment->therapist_id === $user->therapist->id) {
            return true;
        }

        return false;
    }

    protected function recalculateTherapistRating(?Therapist $therapist): void
    {
        if (!$therapist) {
            return;
        }

        $stats = $therapist->reviews()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as reviews_count')
            ->first();

        $therapist->update([
            'rating' => round((float) ($stats->avg_rating ?? 0), 2),
            'rating_count' => (int) ($stats->reviews_count ?? 0),
        ]);
    }

    /**
     * Get list of patients that the therapist has worked with
     */
    public function getTherapistPatients(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTherapist() && !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $therapistId = $user->isTherapist() ? $user->therapist->id : $request->get('therapist_id');

        if (!$therapistId) {
            return response()->json(['message' => 'Therapist ID is required'], 400);
        }

        // Get all appointment IDs for this therapist
        $appointmentIds = \DB::table('appointments')
            ->where('therapist_id', $therapistId)
            ->pluck('id')
            ->toArray();

        if (empty($appointmentIds)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'last_patient' => null
            ]);
        }

        // Get all sessions for these appointments
        $sessions = Session::whereIn('appointment_id', $appointmentIds)
            ->with(['appointment.client'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unique patients with their last session info
        $patientsMap = [];
        $lastPatientId = null;
        $lastSessionDate = null;

        foreach ($sessions as $session) {
            $client = $session->appointment->client;
            if (!$client) continue;

            $clientId = $client->id;

            if (!isset($patientsMap[$clientId])) {
                $patientsMap[$clientId] = [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'avatar' => $client->avatar,
                    'last_session_date' => $session->created_at,
                    'total_sessions' => 0,
                    'upcoming_session' => null
                ];
            }

            $patientsMap[$clientId]['total_sessions']++;

            // Track the most recent patient
            if (!$lastSessionDate || $session->created_at > $lastSessionDate) {
                $lastSessionDate = $session->created_at;
                $lastPatientId = $clientId;
            }

            // Track upcoming session
            if ($session->status === 'scheduled' || $session->status === 'active') {
                if (!$patientsMap[$clientId]['upcoming_session'] || 
                    $session->appointment->starts_at > $patientsMap[$clientId]['upcoming_session']) {
                    $patientsMap[$clientId]['upcoming_session'] = $session->appointment->starts_at;
                }
            }
        }

        $patients = array_values($patientsMap);

        // Sort by last session date (most recent first)
        usort($patients, function($a, $b) {
            return $b['last_session_date'] <=> $a['last_session_date'];
        });

        return response()->json([
            'success' => true,
            'data' => $patients,
            'last_patient' => $lastPatientId
        ]);
    }

    /**
     * Get patient full report (basic info, detailed info, and assessments)
     */
    public function getPatientReport(Request $request, $patientId): JsonResponse
    {
        $user = $request->user();

        if (!$user->isTherapist() && !$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Verify therapist has worked with this patient
        if ($user->isTherapist()) {
            $therapistId = $user->therapist->id;
            $appointmentIds = \DB::table('appointments')
                ->where('therapist_id', $therapistId)
                ->where('client_id', $patientId)
                ->pluck('id')
                ->toArray();

            if (empty($appointmentIds)) {
                return response()->json(['message' => 'You have not worked with this patient'], 403);
            }
        }

        // Get patient user data
        $patient = \App\Models\User::with('patient')->findOrFail($patientId);

        // Get patient assessments - First get all assessments
        $allAssessments = \App\Models\UserAssessment::where('user_id', $patientId)
            ->orderBy('completed_at', 'desc')
            ->get();

        \Log::info('Patient Report - Raw Assessments', [
            'patient_id' => $patientId,
            'total_assessments' => $allAssessments->count(),
            'assessment_ids' => $allAssessments->pluck('id')->toArray(),
            'scale_ids' => $allAssessments->pluck('scale_id')->toArray()
        ]);

        // Load scales separately to ensure they're loaded
        $scaleIds = $allAssessments->pluck('scale_id')->unique()->filter();
        $scales = \App\Models\PsychologicalScale::whereIn('id', $scaleIds)
            ->with('category')
            ->get()
            ->keyBy('id');

        \Log::info('Patient Report - Loaded Scales', [
            'scale_ids_requested' => $scaleIds->toArray(),
            'scales_found' => $scales->count(),
            'scale_ids_found' => $scales->keys()->toArray()
        ]);

        // Map assessments with their scales
        $assessments = $allAssessments
            ->filter(function($assessment) use ($scales) {
                // Only include assessments where we found the scale
                return $scales->has($assessment->scale_id);
            })
            ->map(function($assessment) use ($scales) {
                $scale = $scales->get($assessment->scale_id);
                return [
                    'id' => $assessment->id,
                    'scale_id' => $assessment->scale_id,
                    'scale_name_ar' => $scale->name_ar ?? '',
                    'scale_name_en' => $scale->name_en ?? '',
                    'total_score' => $assessment->total_score,
                    'max_score' => $scale->max_score ?? 100,
                    'interpretation_level' => $assessment->interpretation_level ?? 'غير معروف',
                    'completed_at' => $assessment->completed_at ? $assessment->completed_at->format('Y-m-d H:i:s') : null,
                    'category' => $scale->category ? [
                        'id' => $scale->category->id,
                        'name_ar' => $scale->category->name_ar ?? '',
                        'name_en' => $scale->category->name_en ?? ''
                    ] : null
                ];
            })
            ->values(); // Reset array keys after filtering

        // Log for debugging
        \Log::info('Patient Report - Final Assessments', [
            'patient_id' => $patientId,
            'assessments_count' => $assessments->count(),
            'assessments' => $assessments->toArray()
        ]);

        // Get sessions with this patient
        $appointmentIds = \DB::table('appointments')
            ->where('client_id', $patientId)
            ->pluck('id')
            ->toArray();

        $sessions = [];
        if (!empty($appointmentIds)) {
            $sessions = Session::whereIn('appointment_id', $appointmentIds)
                ->with(['appointment.therapist'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($session) {
                    return [
                        'id' => $session->id,
                        'date' => $session->created_at->format('Y-m-d'),
                        'status' => $session->status,
                        'progress_score' => $session->progress_score ?? 0,
                        'therapist_notes' => $session->therapist_notes ?? ''
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'avatar' => $patient->avatar,
                    'gender' => $patient->gender,
                    'date_of_birth' => $patient->date_of_birth,
                    'country' => $patient->country,
                    'city' => $patient->city,
                    'governorate' => $patient->governorate,
                    'district' => $patient->district,
                    'marital_status' => $patient->marital_status,
                    'education_level' => $patient->education_level,
                    'employment_status' => $patient->employment_status,
                    'profession' => $patient->profession,
                    'monthly_income' => $patient->monthly_income,
                    'platform_purposes' => $patient->platform_purposes,
                    'joined_at' => $patient->joined_at?->format('Y-m-d'),
                ],
                'assessments' => $assessments,
                'recent_sessions' => $sessions
            ]
        ]);
    }
}

