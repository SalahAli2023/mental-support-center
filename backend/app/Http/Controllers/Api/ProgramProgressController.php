<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProgram;
use App\Models\Program;
use App\Models\ProgramPhase;
use App\Models\ProgramSession;
use App\Models\SessionActivity;
use App\Models\ActivityProgress;
use App\Models\SessionCompletion;
use App\Models\SessionHomework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProgramProgressController extends Controller
{
    /**
     * الحصول على تقدم المستخدم في برنامج معين
     */
    public function getUserProgress($programId): JsonResponse
    {
        try {
            $userId = auth()->id();
            $userProgram = UserProgram::where('user_id', $userId)
                                     ->where('program_id', $programId)
                                     ->with([
                                         'program.phases.sessions.activities',
                                         'program.phases.sessions.homework',
                                         'currentPhase',
                                         'currentSession',
                                         'currentActivity'
                                     ])
                                     ->firstOrFail();

            // حساب التقدم التفصيلي
            $progress = $this->calculateDetailedProgress($userProgram, $userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'user_program' => $userProgram,
                    'progress' => $progress,
                    'next_unlocked' => $this->getNextUnlocked($userProgram, $userId)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التقدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * بدء نشاط معين
     */
    public function startActivity(Request $request, $programId, $activityId): JsonResponse
    {
        try {
            $userId = auth()->id();
            $userProgram = UserProgram::where('user_id', $userId)
                                     ->where('program_id', $programId)
                                     ->firstOrFail();

            $activity = SessionActivity::findOrFail($activityId);

            // التحقق من أن النشاط غير مقفل
            if (!$this->isActivityUnlocked($activity, $userId, $userProgram)) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا النشاط مقفل. يجب إكمال الأنشطة السابقة أولاً'
                ], 403);
            }

            DB::beginTransaction();

            // إنشاء أو تحديث تقدم النشاط
            $progress = ActivityProgress::updateOrCreate(
                [
                    'activity_id' => $activityId,
                    'user_id' => $userId
                ],
                [
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'progress_percentage' => 0
                ]
            );

            // تحديث النقطة الحالية في البرنامج
            $userProgram->update([
                'current_activity_id' => $activityId,
                'current_session_id' => $activity->session_id,
                'current_phase_id' => $activity->session->phase_id,
                'status' => 'in_progress',
                'started_at' => $userProgram->started_at ?? now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم بدء النشاط بنجاح',
                'data' => $progress
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء بدء النشاط: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إكمال نشاط معين
     */
    public function completeActivity(Request $request, $programId, $activityId): JsonResponse
    {
        try {
            $userId = auth()->id();
            $userProgram = UserProgram::where('user_id', $userId)
                                     ->where('program_id', $programId)
                                     ->firstOrFail();

            $activity = SessionActivity::findOrFail($activityId);

            DB::beginTransaction();

            // تحديث تقدم النشاط
            $progress = ActivityProgress::updateOrCreate(
                [
                    'activity_id' => $activityId,
                    'user_id' => $userId
                ],
                [
                    'status' => 'completed',
                    'completed_at' => now(),
                    'progress_percentage' => 100,
                    'progress_data' => $request->progress_data ?? null
                ]
            );

            // التحقق من إكمال جميع الأنشطة الإلزامية في الجلسة
            $this->checkSessionCompletion($activity->session_id, $userId, $userProgram);

            // تحديث التقدم العام للبرنامج
            $this->updateProgramProgress($userProgram);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إكمال النشاط بنجاح',
                'data' => $progress,
                'next_activity' => $this->getNextActivity($activity, $userId, $userProgram)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إكمال النشاط: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * التحقق من حالة فتح/إقفال نشاط
     */
    public function checkActivityStatus($programId, $activityId): JsonResponse
    {
        try {
            $userId = auth()->id();
            $userProgram = UserProgram::where('user_id', $userId)
                                     ->where('program_id', $programId)
                                     ->firstOrFail();

            $activity = SessionActivity::findOrFail($activityId);

            $isUnlocked = $this->isActivityUnlocked($activity, $userId, $userProgram);
            $unlockAt = $this->getActivityUnlockAt($activity, $userId, $userProgram);
            $progress = ActivityProgress::where('activity_id', $activityId)
                                       ->where('user_id', $userId)
                                       ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'is_unlocked' => $isUnlocked,
                    'progress' => $progress,
                    'status' => $progress ? $progress->status : 'not_started',
                    'unlock_at' => $unlockAt?->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حساب التقدم التفصيلي
     */
    private function calculateDetailedProgress($userProgram, $userId)
    {
        $program = $userProgram->program;
        $phases = $program->phases;
        
        $totalPhases = $phases->count();
        $completedPhases = 0;
        $totalSessions = 0;
        $completedSessions = 0;
        $totalActivities = 0;
        $completedActivities = 0;
        $totalHomework = 0;
        $completedHomework = 0;

        foreach ($phases as $phase) {
            $phaseSessions = $phase->sessions;
            $totalSessions += $phaseSessions->count();
            
            $phaseCompleted = true;
            foreach ($phaseSessions as $session) {
                $sessionCompleted = SessionCompletion::where('session_id', $session->id)
                                                      ->where('user_id', $userId)
                                                      ->where('is_completed', true)
                                                      ->exists();
                
                if ($sessionCompleted) {
                    $completedSessions++;
                } else {
                    $phaseCompleted = false;
                }

                // الأنشطة
                $sessionActivities = $session->activities;
                $totalActivities += $sessionActivities->count();
                
                foreach ($sessionActivities as $activity) {
                    $activityCompleted = ActivityProgress::where('activity_id', $activity->id)
                                                        ->where('user_id', $userId)
                                                        ->where('status', 'completed')
                                                        ->exists();
                    if ($activityCompleted) {
                        $completedActivities++;
                    }
                }

                // المهام المنزلية
                $sessionHomework = $session->homework;
                $totalHomework += $sessionHomework->where('is_mandatory', true)->count();
                
                foreach ($sessionHomework->where('is_mandatory', true) as $homework) {
                    $homeworkCompleted = HomeworkSubmission::where('homework_id', $homework->id)
                                                          ->where('user_id', $userId)
                                                          ->whereIn('status', ['completed', 'approved'])
                                                          ->exists();
                    if ($homeworkCompleted) {
                        $completedHomework++;
                    }
                }
            }

            if ($phaseCompleted && $totalSessions > 0) {
                $completedPhases++;
            }
        }

        return [
            'phases' => [
                'total' => $totalPhases,
                'completed' => $completedPhases,
                'percentage' => $totalPhases > 0 ? round(($completedPhases / $totalPhases) * 100, 2) : 0
            ],
            'sessions' => [
                'total' => $totalSessions,
                'completed' => $completedSessions,
                'percentage' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0
            ],
            'activities' => [
                'total' => $totalActivities,
                'completed' => $completedActivities,
                'percentage' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0
            ],
            'homework' => [
                'total' => $totalHomework,
                'completed' => $completedHomework,
                'percentage' => $totalHomework > 0 ? round(($completedHomework / $totalHomework) * 100, 2) : 0
            ]
        ];
    }

    /**
     * التحقق من فتح نشاط
     */
    private function isActivityUnlocked($activity, $userId, $userProgram)
    {
        $session = $activity->session;
        $program = $userProgram->program ?? Program::find($userProgram->program_id);
        
        // التحقق من إكمال جميع الأنشطة السابقة في نفس الجلسة
        $previousActivities = SessionActivity::where('session_id', $session->id)
                                             ->where('activity_order', '<', $activity->activity_order)
                                             ->where('is_mandatory', true)
                                             ->get();

        foreach ($previousActivities as $prevActivity) {
            $prevProgress = ActivityProgress::where('activity_id', $prevActivity->id)
                                           ->where('user_id', $userId)
                                           ->where('status', 'completed')
                                           ->exists();
            
            if (!$prevProgress) {
                return false;
            }
        }

        $activityGapHours = (int) ($program?->activity_gap_hours ?? 0);
        if ($activityGapHours > 0 && $previousActivities->isNotEmpty()) {
            $previousActivity = $previousActivities->sortByDesc('activity_order')->first();
            $previousProgress = ActivityProgress::where('activity_id', $previousActivity->id)
                                                ->where('user_id', $userId)
                                                ->where('status', 'completed')
                                                ->first();

            if (!$previousProgress || !$previousProgress->completed_at) {
                return false;
            }

            if (now()->lessThan($previousProgress->completed_at->copy()->addHours($activityGapHours))) {
                return false;
            }
        }

        // التحقق من إكمال الجلسة السابقة
        $previousSession = ProgramSession::where('phase_id', $session->phase_id)
                                        ->where('session_order', '<', $session->session_order)
                                        ->orderBy('session_order', 'desc')
                                        ->first();

        if ($previousSession) {
            $sessionCompleted = SessionCompletion::where('session_id', $previousSession->id)
                                                ->where('user_id', $userId)
                                                ->where('is_completed', true)
                                                ->first();
            
            if (!$sessionCompleted) {
                return false;
            }
            
            $sessionGapHours = (int) ($program?->session_gap_hours ?? 0);
            if ($sessionGapHours > 0 && $sessionCompleted->completed_at) {
                if (now()->lessThan($sessionCompleted->completed_at->copy()->addHours($sessionGapHours))) {
                    return false;
                }
            }
        }

        // التحقق من إكمال المرحلة السابقة
        $phase = $session->phase;
        $previousPhase = ProgramPhase::where('program_id', $userProgram->program_id)
                                     ->where('phase_order', '<', $phase->phase_order)
                                     ->orderBy('phase_order', 'desc')
                                     ->first();

        if ($previousPhase) {
            // التحقق من إكمال جميع الجلسات في المرحلة السابقة
            $previousPhaseSessions = $previousPhase->sessions;
            foreach ($previousPhaseSessions as $prevSession) {
                $prevSessionCompleted = SessionCompletion::where('session_id', $prevSession->id)
                                                         ->where('user_id', $userId)
                                                         ->where('is_completed', true)
                                                         ->exists();
                
                if (!$prevSessionCompleted) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getActivityUnlockAt($activity, $userId, $userProgram)
    {
        $session = $activity->session;
        $program = $userProgram->program ?? Program::find($userProgram->program_id);

        if (!$session || !$program) {
            return null;
        }

        $activityGapHours = (int) ($program->activity_gap_hours ?? 0);
        if ($activityGapHours > 0 && $activity->activity_order > 1) {
            $previousActivity = SessionActivity::where('session_id', $session->id)
                                               ->where('activity_order', '<', $activity->activity_order)
                                               ->orderBy('activity_order', 'desc')
                                               ->first();

            if ($previousActivity) {
                $previousProgress = ActivityProgress::where('activity_id', $previousActivity->id)
                                                   ->where('user_id', $userId)
                                                   ->where('status', 'completed')
                                                   ->first();

                if ($previousProgress && $previousProgress->completed_at) {
                    return $previousProgress->completed_at->copy()->addHours($activityGapHours);
                }
            }
        }

        $sessionGapHours = (int) ($program->session_gap_hours ?? 0);
        if ($sessionGapHours > 0 && $session->session_order > 1) {
            $previousSession = ProgramSession::where('phase_id', $session->phase_id)
                                            ->where('session_order', '<', $session->session_order)
                                            ->orderBy('session_order', 'desc')
                                            ->first();

            if ($previousSession) {
                $sessionCompleted = SessionCompletion::where('session_id', $previousSession->id)
                                                    ->where('user_id', $userId)
                                                    ->where('is_completed', true)
                                                    ->first();

                if ($sessionCompleted && $sessionCompleted->completed_at) {
                    return $sessionCompleted->completed_at->copy()->addHours($sessionGapHours);
                }
            }
        }

        return null;
    }

    /**
     * التحقق من إكمال الجلسة
     */
    private function checkSessionCompletion($sessionId, $userId, $userProgram)
    {
        $session = ProgramSession::findOrFail($sessionId);
        
        // التحقق من إكمال جميع الأنشطة الإلزامية
        $mandatoryActivities = $session->activities()->where('is_mandatory', true)->get();
        $allActivitiesCompleted = true;

        foreach ($mandatoryActivities as $activity) {
            $activityCompleted = ActivityProgress::where('activity_id', $activity->id)
                                                ->where('user_id', $userId)
                                                ->where('status', 'completed')
                                                ->exists();
            
            if (!$activityCompleted) {
                $allActivitiesCompleted = false;
                break;
            }
        }

        // التحقق من إكمال جميع المهام الإلزامية
        $mandatoryHomework = $session->homework()->where('is_mandatory', true)->get();
        $allHomeworkCompleted = true;

        foreach ($mandatoryHomework as $homework) {
            $homeworkCompleted = HomeworkSubmission::where('homework_id', $homework->id)
                                                  ->where('user_id', $userId)
                                                  ->whereIn('status', ['completed', 'approved'])
                                                  ->exists();
            
            if (!$homeworkCompleted) {
                $allHomeworkCompleted = false;
                break;
            }
        }

        // إذا تم إكمال كل شيء، قم بتسجيل إكمال الجلسة
        if ($allActivitiesCompleted && $allHomeworkCompleted) {
            SessionCompletion::updateOrCreate(
                [
                    'session_id' => $sessionId,
                    'user_id' => $userId
                ],
                [
                    'is_completed' => true,
                    'completed_at' => now()
                ]
            );
        }
    }

    /**
     * تحديث التقدم العام للبرنامج
     */
    private function updateProgramProgress($userProgram)
    {
        $progress = $this->calculateDetailedProgress($userProgram, $userProgram->user_id);
        
        // حساب النسبة المئوية الإجمالية
        $totalProgress = (
            $progress['phases']['percentage'] * 0.3 +
            $progress['sessions']['percentage'] * 0.3 +
            $progress['activities']['percentage'] * 0.3 +
            $progress['homework']['percentage'] * 0.1
        );

        $status = 'in_progress';
        if ($totalProgress >= 100) {
            $status = 'completed';
            $userProgram->completed_at = now();
        }

        $userProgram->update([
            'progress_percentage' => round($totalProgress),
            'status' => $status,
            'progress_data' => $progress
        ]);
    }

    /**
     * الحصول على النشاط التالي
     */
    private function getNextActivity($currentActivity, $userId, $userProgram)
    {
        $session = $currentActivity->session;
        
        // البحث عن نشاط تالي في نفس الجلسة
        $nextActivity = SessionActivity::where('session_id', $session->id)
                                       ->where('activity_order', '>', $currentActivity->activity_order)
                                       ->where('is_active', true)
                                       ->orderBy('activity_order')
                                       ->first();

        if ($nextActivity) {
            return $nextActivity;
        }

        // إذا لم يكن هناك نشاط تالي، ابحث عن جلسة تالية
        $nextSession = ProgramSession::where('phase_id', $session->phase_id)
                                     ->where('session_order', '>', $session->session_order)
                                     ->orderBy('session_order')
                                     ->first();

        if ($nextSession) {
            $firstActivity = $nextSession->activities()
                                        ->where('is_active', true)
                                        ->orderBy('activity_order')
                                        ->first();
            return $firstActivity;
        }

        return null;
    }

    /**
     * الحصول على العنصر التالي غير المقفل
     */
    private function getNextUnlocked($userProgram, $userId)
    {
        $program = $userProgram->program;
        $phases = $program->phases()->orderBy('phase_order')->get();

        foreach ($phases as $phase) {
            $sessions = $phase->sessions()->orderBy('session_order')->get();
            
            foreach ($sessions as $session) {
                // التحقق من إكمال الجلسة
                $sessionCompleted = SessionCompletion::where('session_id', $session->id)
                                                    ->where('user_id', $userId)
                                                    ->where('is_completed', true)
                                                    ->exists();

                if (!$sessionCompleted) {
                    // البحث عن أول نشاط غير مكتمل
                    $activities = $session->activities()
                                         ->where('is_active', true)
                                         ->orderBy('activity_order')
                                         ->get();

                    foreach ($activities as $activity) {
                        $activityCompleted = ActivityProgress::where('activity_id', $activity->id)
                                                           ->where('user_id', $userId)
                                                           ->where('status', 'completed')
                                                           ->exists();

                        if (!$activityCompleted) {
                            return [
                                'type' => 'activity',
                                'phase_id' => $phase->id,
                                'session_id' => $session->id,
                                'activity_id' => $activity->id,
                                'is_unlocked' => $this->isActivityUnlocked($activity, $userId, $userProgram)
                            ];
                        }
                    }
                }
            }
        }

        return null;
    }
}




