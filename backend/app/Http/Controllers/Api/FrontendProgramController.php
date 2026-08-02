<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramSession;
use App\Models\SessionActivity;
use App\Models\UserProgram;
use App\Models\ActivitySubmission;
use App\Models\SessionCompletion;
use App\Models\ActivityProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FrontendProgramController extends Controller
{
    // ==================== 1. عرض جميع البرامج ====================
    
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Program::query()
                ->with(['scale'])
                ->orderBy('created_at', 'desc');
            
            // البحث
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 12);
            $programs = $query->paginate($perPage);
            
            // تحويل البيانات مع الصور الكاملة
            $transformedPrograms = $programs->map(function ($program) {
                return $this->transformProgram($program);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل البرامج بنجاح',
                'data' => $transformedPrograms,
                'meta' => [
                    'current_page' => $programs->currentPage(),
                    'last_page' => $programs->lastPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in programs index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل البرامج'
            ], 500);
        }
    }
    
    // ==================== 2. عرض برنامج معين ====================
    
    public function show($id): JsonResponse
    {
        try {
            $program = Program::where('id', $id)
                ->with(['scale', 'sessions' => function($query) {
                    $query->orderBy('session_order')
                          ->with(['activities']);
                }])
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            // تحويل بيانات البرنامج
            $programData = $this->transformProgram($program, true);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل تفاصيل البرنامج',
                'data' => $programData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in program show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل تفاصيل البرنامج'
            ], 500);
        }
    }
    
    // ==================== 3. عرض جلسات البرنامج ====================
    
    public function programSessions($programId): JsonResponse
    {
        try {
            $program = Program::where('id', $programId)
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            // الحصول على الجلسات مع الأنشطة
            $sessions = ProgramSession::where('program_id', $programId)
                ->orderBy('session_order')
                ->with(['activities'])
                ->get();
            
            // تحويل بيانات الجلسات
            $user = Auth::guard('sanctum')->user();
            $transformedSessions = $sessions->map(function ($session, $index) use ($user, $programId) {
                return $this->transformSession($session, $index, $user, $programId);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل جلسات البرنامج',
                'data' => [
                    'program' => [
                        'id' => $program->id,
                        'name_ar' => $program->name_ar,
                        'name_en' => $program->name_en,
                        'image_url' => $this->getFullImageUrl($program->image_url),
                        'total_sessions' => $sessions->count()
                    ],
                    'sessions' => $transformedSessions
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in program sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل الجلسات'
            ], 500);
        }
    }
    
    // ==================== 4. عرض جلسة معينة ====================
    
    public function showSession($programId, $sessionId): JsonResponse
    {
        try {
            $session = ProgramSession::where('id', $sessionId)
                ->where('program_id', $programId)
                ->with(['activities'])
                ->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }
            
            $user = Auth::guard('sanctum')->user();
            $sessionData = $this->transformSession($session, $session->session_order - 1, $user, $programId);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل تفاصيل الجلسة',
                'data' => $sessionData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in show session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل الجلسة'
            ], 500);
        }
    }
    
    // ==================== 5. عرض نشاط معين ====================
    
    public function showActivity($programId, $sessionId, $activityId): JsonResponse
    {
        try {
            $activity = SessionActivity::where('id', $activityId)
                ->where('session_id', $sessionId)
                ->first();
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'النشاط غير موجود'
                ], 404);
            }
            
            $user = Auth::guard('sanctum')->user();
            $activityData = $this->transformActivity($activity, $user);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل النشاط',
                'data' => $activityData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in show activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل النشاط'
            ], 500);
        }
    }
    
    // ==================== 6. التسجيل في برنامج ====================
    
    public function enroll(Request $request, $programId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $program = Program::where('id', $programId)
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            // التحقق من التسجيل المسبق
            $existingEnrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
                ->first();
            
            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت مسجل بالفعل في هذا البرنامج'
                ], 400);
            }
            
            // إنشاء تسجيل جديد
            $enrollment = UserProgram::create([
                'user_id' => $user->id,
                'program_id' => $programId,
                'enrollment_date' => now(),
                'status' => 'enrolled',
                'progress_percentage' => 0,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل في البرنامج بنجاح',
                'data' => [
                    'enrollment_id' => $enrollment->id,
                    'program_id' => $program->id,
                    'program_name' => $program->name_ar,
                    'program_image_url' => $this->getFullImageUrl($program->image_url),
                    'status' => $enrollment->status,
                    'enrollment_date' => $enrollment->enrollment_date
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in enroll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في التسجيل'
            ], 500);
        }
    }
    
    // ==================== 7. برامج المستخدم ====================
    
    public function myPrograms(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $query = UserProgram::where('user_id', $user->id)
                ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
                ->with(['program'])
                ->orderBy('created_at', 'desc');
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $enrollments = $query->paginate($perPage);
            
            // تحويل البيانات مع الصور الكاملة
            $transformedEnrollments = $enrollments->map(function ($enrollment) {
                $program = $enrollment->program;
                
                return [
                    'id' => $enrollment->id,
                    'program_id' => $program->id,
                    'program_name_ar' => $program->name_ar,
                    'program_name_en' => $program->name_en,
                    'program_image_url' => $this->getFullImageUrl($program->image_url),
                    'enrollment_status' => $enrollment->status,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'enrollment_date' => $enrollment->enrollment_date,
                    'total_sessions' => $program->sessions()->count(),
                    'is_completed' => $enrollment->status === 'completed'
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل برامجك',
                'data' => $transformedEnrollments,
                'meta' => [
                    'current_page' => $enrollments->currentPage(),
                    'last_page' => $enrollments->lastPage(),
                    'per_page' => $enrollments->perPage(),
                    'total' => $enrollments->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in my programs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل برامجك'
            ], 500);
        }
    }
    
    // ==================== 8. تقديم نشاط ====================
    
    public function submitActivity(Request $request, $programId, $sessionId, $activityId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $validated = $request->validate([
                'notes' => 'nullable|string'
            ]);
            
            // البحث عن النشاط
            $activity = SessionActivity::where('id', $activityId)
                ->where('session_id', $sessionId)
                ->first();
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'النشاط غير موجود'
                ], 404);
            }
            
            // التحقق من تسجيل المستخدم في البرنامج
            $enrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->whereIn('status', ['enrolled', 'in_progress'])
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت غير مسجل في هذا البرنامج'
                ], 403);
            }
            
            // إنشاء أو تحديث التقديم
            $submission = ActivitySubmission::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activityId
                ],
                [
                    'status' => 'submitted',
                    'submission_date' => now(),
                    'notes' => $request->notes
                ]
            );
            
            // تحديث تقدم المستخدم
            $this->updateUserProgress($user->id, $programId);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تقديم النشاط بنجاح',
                'data' => [
                    'submission_id' => $submission->id,
                    'activity_id' => $activityId,
                    'status' => $submission->status,
                    'submission_date' => $submission->submission_date
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in submit activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تقديم النشاط'
            ], 500);
        }
    }
    
    // ==================== 9. إكمال جلسة ====================
    
    public function completeSession(Request $request, $programId, $sessionId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            // التحقق من الجلسة
            $session = ProgramSession::where('id', $sessionId)
                ->where('program_id', $programId)
                ->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }
            
            // التحقق من إكمال جميع الأنشطة الإلزامية
            $mandatoryActivities = $session->activities()->where('is_mandatory', true)->get();
            $completedMandatoryCount = 0;
            $totalMandatoryActivities = $mandatoryActivities->count();
            
            foreach ($mandatoryActivities as $activity) {
                $submission = ActivitySubmission::where('user_id', $user->id)
                    ->where('activity_id', $activity->id)
                    ->whereIn('status', ['completed', 'approved'])
                    ->exists();
                
                if ($submission) {
                    $completedMandatoryCount++;
                }
            }
            
            // إذا لم يكمل جميع الأنشطة الإلزامية
            if ($completedMandatoryCount < $totalMandatoryActivities && !$request->boolean('force', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب إكمال جميع الأنشطة الإلزامية أولاً',
                    'data' => [
                        'completed_mandatory' => $completedMandatoryCount,
                        'total_mandatory' => $totalMandatoryActivities,
                        'remaining' => $totalMandatoryActivities - $completedMandatoryCount
                    ]
                ], 400);
            }
            
            // إنشاء أو تحديث سجل إكمال الجلسة
            SessionCompletion::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'session_id' => $sessionId
                ],
                [
                    'is_completed' => true,
                    'completed_at' => now()
                ]
            );
            
            // تحديث تقدم المستخدم في البرنامج
            $this->updateUserProgress($user->id, $programId);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إكمال الجلسة بنجاح',
                'data' => [
                    'session_id' => $sessionId,
                    'session_title' => $session->title_ar,
                    'session_image_url' => $this->getFullImageUrl($session->image_url),
                    'completed_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in complete session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إكمال الجلسة'
            ], 500);
        }
    }
    
    // ==================== 10. تحديث تقدم البرنامج ====================
    
    public function updateProgress($programId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $program = Program::where('id', $programId)
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            $progressData = $this->calculateUserProgress($user->id, $programId);
            
            // تحديث تقدم المستخدم في البرنامج
            $enrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->first();
            
            if ($enrollment) {
                $enrollment->update([
                    'progress_percentage' => $progressData['progress_percentage'],
                    'status' => $progressData['progress_percentage'] >= 100 ? 'completed' : 
                               ($progressData['progress_percentage'] > 0 ? 'in_progress' : 'enrolled')
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التقدم',
                'data' => [
                    'program_id' => $programId,
                    'program_name' => $program->name_ar,
                    'program_image_url' => $this->getFullImageUrl($program->image_url),
                    'progress_percentage' => $progressData['progress_percentage'],
                    'completed_activities' => $progressData['completed_activities'],
                    'total_activities' => $progressData['total_activities'],
                    'completed_sessions' => $progressData['completed_sessions'],
                    'status' => $enrollment->status ?? 'not_enrolled'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in update progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث التقدم'
            ], 500);
        }
    }
    
    // ==================== 11. إلغاء التسجيل ====================
    
    public function unenroll($programId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $enrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت غير مسجل في هذا البرنامج'
                ], 404);
            }
            
            $enrollment->update([
                'status' => 'dropped'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء التسجيل بنجاح',
                'data' => [
                    'program_id' => $programId
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in unenroll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إلغاء التسجيل'
            ], 500);
        }
    }
    
    // ==================== 12. معرض صور البرنامج ====================

    public function programImages($programId): JsonResponse
    {
        try {
            $program = Program::where('id', $programId)
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            $images = [];
            
            // 1. صورة البرنامج الرئيسية
            if ($program->image_url) {
                $images[] = [
                    'id' => 'program_main',
                    'type' => 'program',
                    'title_ar' => $program->name_ar,
                    'title_en' => $program->name_en,
                    'image_url' => $this->getFullImageUrl($program->image_url),
                    'description_ar' => 'صورة البرنامج الرئيسية',
                    'description_en' => 'Main program image',
                    'order' => 0,
                    'created_at' => $program->created_at
                ];
            }
            
            // 2. صور الجلسات
            $sessions = ProgramSession::where('program_id', $programId)
                ->whereNotNull('image_url')
                ->orderBy('session_order')
                ->get(['id', 'session_order', 'title_ar', 'title_en', 'image_url', 'goal_ar', 'goal_en']);
            
            foreach ($sessions as $session) {
                $images[] = [
                    'id' => 'session_' . $session->id,
                    'type' => 'session',
                    'title_ar' => $session->title_ar,
                    'title_en' => $session->title_en,
                    'image_url' => $this->getFullImageUrl($session->image_url),
                    'description_ar' => $session->goal_ar,
                    'description_en' => $session->goal_en,
                    'order' => $session->session_order,
                    'session_order' => $session->session_order,
                    'created_at' => $session->created_at
                ];
            }
            
            // ترتيب الصور حسب الترتيب
            usort($images, function($a, $b) {
                return $a['order'] <=> $b['order'];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل صور البرنامج',
                'data' => [
                    'program' => [
                        'id' => $program->id,
                        'name_ar' => $program->name_ar,
                        'name_en' => $program->name_en,
                        'total_images' => count($images)
                    ],
                    'images' => $images,
                    'count' => count($images)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in program images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل صور البرنامج'
            ], 500);
        }
    }
    
    // ==================== 13. تقدم المستخدم في برنامج ====================
    
    public function programProgress($programId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $program = Program::where('id', $programId)
                ->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }
            
            // التحقق من التسجيل
            $enrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $programId)
                ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت غير مسجل في هذا البرنامج'
                ], 403);
            }
            
            // حساب التقدم التفصيلي
            $progressData = $this->calculateUserProgress($user->id, $programId);
            
            // جلسات البرنامج مع تقدم المستخدم
            $sessions = ProgramSession::where('program_id', $programId)
                ->orderBy('session_order')
                ->get(['id', 'session_order', 'title_ar', 'title_en', 'image_url']);
            
            $sessionsWithProgress = $sessions->map(function($session) use ($user) {
                $sessionCompletion = SessionCompletion::where('user_id', $user->id)
                    ->where('session_id', $session->id)
                    ->where('is_completed', true)
                    ->exists();
                
                $sessionActivities = SessionActivity::where('session_id', $session->id)
                    ->count();
                
                $completedActivities = ActivitySubmission::where('user_id', $user->id)
                    ->whereHas('activity', function($q) use ($session) {
                        $q->where('session_id', $session->id);
                    })
                    ->whereIn('status', ['completed', 'approved'])
                    ->count();
                
                $sessionProgress = $sessionActivities > 0 
                    ? round(($completedActivities / $sessionActivities) * 100, 2) 
                    : 0;
                
                return [
                    'id' => $session->id,
                    'order' => $session->session_order,
                    'title_ar' => $session->title_ar,
                    'title_en' => $session->title_en,
                    'image_url' => $this->getFullImageUrl($session->image_url),
                    'is_completed' => $sessionCompletion,
                    'total_activities' => $sessionActivities,
                    'completed_activities' => $completedActivities,
                    'progress_percentage' => $sessionProgress
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل تقدم المستخدم',
                'data' => [
                    'program' => [
                        'id' => $program->id,
                        'name_ar' => $program->name_ar,
                        'name_en' => $program->name_en,
                        'image_url' => $this->getFullImageUrl($program->image_url),
                    ],
                    'enrollment' => [
                        'id' => $enrollment->id,
                        'status' => $enrollment->status,
                        'enrollment_date' => $enrollment->enrollment_date,
                        'progress_percentage' => $enrollment->progress_percentage
                    ],
                    'progress' => [
                        'overall_percentage' => $progressData['progress_percentage'],
                        'total_sessions' => $progressData['total_sessions'],
                        'completed_sessions' => $progressData['completed_sessions'],
                        'total_activities' => $progressData['total_activities'],
                        'completed_activities' => $progressData['completed_activities'],
                        'is_completed' => $enrollment->status === 'completed'
                    ],
                    'sessions' => $sessionsWithProgress
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in program progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل التقدم'
            ], 500);
        }
    }
    
    // ==================== 14. إكمال نشاط ====================
    
    public function completeActivity(Request $request, $programId, $sessionId, $activityId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            $validated = $request->validate([
                'notes' => 'nullable|string'
            ]);
            
            // البحث عن النشاط
            $activity = SessionActivity::where('id', $activityId)
                ->where('session_id', $sessionId)
                ->first();
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'النشاط غير موجود'
                ], 404);
            }
            
            // تحديث التقديم إلى حالة completed
            $submission = ActivitySubmission::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activityId
                ],
                [
                    'status' => 'completed',
                    'submission_date' => now(),
                    'notes' => $request->notes
                ]
            );
            
            // تحديث تقدم المستخدم
            $this->updateUserProgress($user->id, $programId);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إكمال النشاط بنجاح',
                'data' => [
                    'submission_id' => $submission->id,
                    'activity_id' => $activityId,
                    'status' => $submission->status,
                    'submission_date' => $submission->submission_date
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in complete activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إكمال النشاط'
            ], 500);
        }
    }
    
    // ==================== 15. إحصائيات المستخدم ====================
    
    public function userStatistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }
            
            // حساب الإحصائيات
            $totalPrograms = UserProgram::where('user_id', $user->id)->count();
            $completedPrograms = UserProgram::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();
            
            $enrolledPrograms = UserProgram::where('user_id', $user->id)
                ->where('status', 'enrolled')
                ->count();
            
            $inProgressPrograms = UserProgram::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count();
            
            $totalSessionsCompleted = SessionCompletion::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();
            
            $totalActivitiesCompleted = ActivitySubmission::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'approved'])
                ->count();
            
            $averageProgress = UserProgram::where('user_id', $user->id)
                ->avg('progress_percentage') ?? 0;
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحميل إحصائيات المستخدم',
                'data' => [
                    'total_programs' => $totalPrograms,
                    'completed_programs' => $completedPrograms,
                    'enrolled_programs' => $enrolledPrograms,
                    'in_progress_programs' => $inProgressPrograms,
                    'total_sessions_completed' => $totalSessionsCompleted,
                    'total_activities_completed' => $totalActivitiesCompleted,
                    'average_progress' => round($averageProgress, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in user statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل الإحصائيات'
            ], 500);
        }
    }
    
    // ==================== الدوال المساعدة ====================
    
    /**
     * تحويل مسار الصورة إلى URL كامل
     */
    private function getFullImageUrl($imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }
        
        // إذا كان المسار يحتوي بالفعل على http أو https، ارجعه كما هو
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }
        
        // إذا كان المسار يبدأ بـ media/، أضف المسار الكامل
        if (strpos($imagePath, 'media/') === 0) {
            return url($imagePath);
        }
        
        // إذا كان المسار يبدأ بـ programs/ فقط، أضف media/ قبلها
        if (strpos($imagePath, 'programs/') === 0) {
            return url('media/' . $imagePath);
        }
        
        // في حالات أخرى، أضف media/programs/ قبل المسار
        return url('media/programs/' . $imagePath);
    }
    
    /**
     * تحويل بيانات البرنامج
     */
    private function transformProgram($program, $detailed = false): array
    {
        $user = Auth::guard('sanctum')->user();
        $isEnrolled = false;
        $userProgress = null;
        
        if ($user) {
            $enrollment = UserProgram::where('user_id', $user->id)
                ->where('program_id', $program->id)
                ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
                ->first();
            
            if ($enrollment) {
                $isEnrolled = true;
                
                $progressData = $this->calculateUserProgress($user->id, $program->id);
                
                $userProgress = [
                    'percentage' => $progressData['progress_percentage'],
                    'completed_sessions' => $progressData['completed_sessions'],
                    'total_sessions' => $progressData['total_sessions'],
                    'completed_activities' => $progressData['completed_activities'],
                    'total_activities' => $progressData['total_activities'],
                    'is_completed' => $enrollment->status === 'completed',
                    'enrollment_id' => $enrollment->id
                ];
            }
        }
        
        // الحصول على URL الكاملة للصورة
        $imageUrl = $this->getFullImageUrl($program->image_url);
        
        $data = [
            'id' => $program->id,
            'name_ar' => $program->name_ar,
            'name_en' => $program->name_en,
            'description_ar' => $program->description_ar,
            'description_en' => $program->description_en,
            'image_url' => $imageUrl, // ✅ URL كامل
            'cover_image' => $imageUrl, // ✅ URL كامل
            'duration' => $program->duration,
            'max_duration_days' => $program->max_duration_days,
            'session_duration_minutes' => $program->session_duration_minutes,
            'session_gap_hours' => $program->session_gap_hours,
            'activity_gap_hours' => $program->activity_gap_hours,
            'status' => $program->status,
            'scale_id' => $program->scale_id,
            'scale' => $program->scale ? [
                'id' => $program->scale->id,
                'name_ar' => $program->scale->name_ar,
                'name_en' => $program->scale->name_en
            ] : null,
            'is_enrolled' => $isEnrolled,
            'user_progress' => $userProgress,
            'sessions_count' => $program->sessions_count ?? $program->sessions->count(),
            'enrollment_count' => $program->userPrograms()->count()
        ];
        
        if ($detailed) {
            $data['total_sessions'] = $program->sessions()->count();
            
            // إضافة صور الجلسات كصور إضافية
            $sessionImages = $program->sessions()
                ->whereNotNull('image_url')
                ->orderBy('session_order')
                ->limit(5)
                ->get(['image_url'])
                ->map(function($session) {
                    return $this->getFullImageUrl($session->image_url);
                })
                ->toArray();
            
            $data['additional_images'] = $sessionImages;
        }
        
        return $data;
    }
    
    /**
     * تحويل بيانات الجلسة
     */
    private function transformSession($session, $index, $user, $programId): array
    {
        $isCompleted = false;
        $completedActivities = 0;
        $unlockAt = null;
        $isUnlocked = true;
        
        if ($user) {
            // التحقق من إكمال الجلسة
            $sessionCompletion = SessionCompletion::where('user_id', $user->id)
                ->where('session_id', $session->id)
                ->where('is_completed', true)
                ->exists();
            
            $isCompleted = $sessionCompletion;
            $unlockAt = $this->getSessionUnlockAt($session, $user, $programId);
            $isUnlocked = !$unlockAt || now()->greaterThanOrEqualTo($unlockAt);
            
            // حساب الأنشطة المكتملة
            if ($session->activities) {
                foreach ($session->activities as $activity) {
                    $submission = ActivitySubmission::where('user_id', $user->id)
                        ->where('activity_id', $activity->id)
                        ->whereIn('status', ['completed', 'approved'])
                        ->exists();
                    
                    if ($submission) {
                        $completedActivities++;
                    }
                }
            }
        }
        
        // الحصول على URL الكاملة لصورة الجلسة
        $imageUrl = $this->getFullImageUrl($session->image_url);
        
        $defaultSessionDuration = $session->duration ?? $session->program?->session_duration_minutes ?? 60;

        return [
            'id' => $session->id,
            'title_ar' => $session->title_ar,
            'title_en' => $session->title_en,
            'description_ar' => $session->goal_ar,
            'description_en' => $session->goal_en,
            'order' => $session->session_order,
            'session_order' => $session->session_order,
            'duration' => $defaultSessionDuration,
            'image_url' => $imageUrl, // ✅ URL كامل
            'activities_count' => $session->activities ? $session->activities->count() : 0,
            'activities' => $session->activities ? $session->activities->map(function($activity) use ($user) {
                return $this->transformActivity($activity, $user);
            }) : [],
            'is_completed' => $isCompleted,
            'is_unlocked' => $isUnlocked,
            'unlock_at' => $unlockAt?->format('Y-m-d H:i:s'),
            'completed_activities' => $completedActivities,
            'progress_percentage' => $session->activities && $session->activities->count() > 0 
                ? round(($completedActivities / $session->activities->count()) * 100, 2) 
                : 0
        ];
    }
    
    /**
     * تحويل بيانات النشاط
     */
    private function transformActivity($activity, $user): array
    {
        $submissionData = null;
        $isCompleted = false;
        $unlockAt = null;
        $isUnlocked = true;
        
        if ($user) {
            $submission = ActivitySubmission::where('user_id', $user->id)
                ->where('activity_id', $activity->id)
                ->first();
            
            if ($submission) {
                $submissionData = [
                    'status' => $submission->status,
                    'submission_date' => $submission->submission_date,
                    'notes' => $submission->notes
                ];
                
                $isCompleted = in_array($submission->status, ['completed', 'approved']);
            }
            
            $unlockAt = $this->getActivityUnlockAt($activity, $user);
            $isUnlocked = !$unlockAt || now()->greaterThanOrEqualTo($unlockAt);
        }
        
        return [
            'id' => $activity->id,
            'name_ar' => $activity->name_ar,
            'name_en' => $activity->name_en,
            'activity_type' => $activity->activity_type,
            'content_ar' => $activity->content_ar,
            'content_en' => $activity->content_en,
            'media_url' => $activity->media_url,
            'media_type' => $activity->media_type,
            'scale_id' => $activity->scale_id,
            'activity_config' => $activity->activity_config,
            'duration_minutes' => $activity->duration_minutes,
            'instructions_ar' => $activity->instructions_ar,
            'instructions_en' => $activity->instructions_en,
            'is_mandatory' => $activity->is_mandatory,
            'submission_data' => $submissionData,
            'is_completed' => $isCompleted,
            'is_unlocked' => $isUnlocked,
            'unlock_at' => $unlockAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function getSessionUnlockAt($session, $user, $programId)
    {
        $program = Program::find($programId);
        if (!$program || !$user) {
            return null;
        }

        $gapHours = (int) ($program->session_gap_hours ?? 0);
        if ($gapHours <= 0 || $session->session_order <= 1) {
            return null;
        }

        $previousSession = ProgramSession::where('phase_id', $session->phase_id)
            ->where('session_order', '<', $session->session_order)
            ->orderBy('session_order', 'desc')
            ->first();

        if (!$previousSession) {
            return null;
        }

        $previousCompletion = SessionCompletion::where('user_id', $user->id)
            ->where('session_id', $previousSession->id)
            ->where('is_completed', true)
            ->first();

        if (!$previousCompletion || !$previousCompletion->completed_at) {
            return null;
        }

        return $previousCompletion->completed_at->copy()->addHours($gapHours);
    }

    private function getActivityUnlockAt($activity, $user)
    {
        $session = $activity->session;
        if (!$session || !$user) {
            return null;
        }

        $program = $session->program;
        $gapHours = (int) ($program?->activity_gap_hours ?? 0);
        if ($gapHours <= 0 || $activity->activity_order <= 1) {
            return null;
        }

        $previousActivity = SessionActivity::where('session_id', $session->id)
            ->where('activity_order', '<', $activity->activity_order)
            ->orderBy('activity_order', 'desc')
            ->first();

        if (!$previousActivity) {
            return null;
        }

        $previousProgress = ActivityProgress::where('activity_id', $previousActivity->id)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if (!$previousProgress || !$previousProgress->completed_at) {
            return null;
        }

        return $previousProgress->completed_at->copy()->addHours($gapHours);
    }
    
    /**
     * حساب تقدم المستخدم
     */
    private function calculateUserProgress($userId, $programId): array
    {
        $totalActivities = DB::table('session_activities')
            ->join('program_sessions', 'session_activities.session_id', '=', 'program_sessions.id')
            ->where('program_sessions.program_id', $programId)
            ->count();
        
        $completedActivities = ActivitySubmission::where('user_id', $userId)
            ->whereHas('activity.session', function($query) use ($programId) {
                $query->where('program_id', $programId);
            })
            ->whereIn('status', ['completed', 'approved'])
            ->count();
        
        $completedSessions = SessionCompletion::where('user_id', $userId)
            ->whereHas('session', function($query) use ($programId) {
                $query->where('program_id', $programId);
            })
            ->where('is_completed', true)
            ->count();
        
        $totalSessions = ProgramSession::where('program_id', $programId)
            ->count();
        
        $progressPercentage = $totalActivities > 0 
            ? round(($completedActivities / $totalActivities) * 100, 2) 
            : 0;
        
        return [
            'progress_percentage' => $progressPercentage,
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions
        ];
    }
    
    /**
     * تحديث تقدم المستخدم
     */
    private function updateUserProgress($userId, $programId): void
    {
        $progressData = $this->calculateUserProgress($userId, $programId);
        
        $enrollment = UserProgram::where('user_id', $userId)
            ->where('program_id', $programId)
            ->first();
        
        if ($enrollment) {
            $enrollment->update([
                'progress_percentage' => $progressData['progress_percentage'],
                'status' => $progressData['progress_percentage'] >= 100 ? 'completed' : 
                           ($progressData['progress_percentage'] > 0 ? 'in_progress' : 'enrolled')
            ]);
        }
    }
}