<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'bio',
        'joined_at',
        'gender',
        'date_of_birth',
        'country',
        'city',
        'governorate',
        'district',
        'marital_status',
        'education_level',
        'employment_status',
        'profession',
        'monthly_income',
        'platform_purposes',
        'terms_accepted',
        'privacy_accepted',
        'info_accuracy_confirmed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joined_at' => 'datetime',
            'date_of_birth' => 'date',
            'platform_purposes' => 'array',
            'terms_accepted' => 'boolean',
            'privacy_accepted' => 'boolean',
            'info_accuracy_confirmed' => 'boolean',
        ];
    }

    // ==================== العلاقات الأساسية ====================

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function therapist()
    {
        return $this->hasOne(Therapist::class);
    }

    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    public function measureResponses()
    {
        return $this->hasMany(MeasureResponse::class);
    }

    public function createdPrograms()
    {
        return $this->hasMany(Program::class, 'user_id');
    }

    public function assignedPrograms()
    {
        return $this->belongsToMany(Program::class, 'program_user', 'user_id', 'program_id')
            ->withTimestamps()
            ->withPivot(['role', 'assigned_by', 'assigned_at', 'status']);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(UserAssessment::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    // ==================== علاقات البرامج والجلسات الجديدة ====================

    /**
     * 🔥 UPDATED: Get the user programs (enrollments in programs).
     */
    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class, 'user_id');
    }

    /**
     * 🔥 UPDATED: Get the programs through user programs (enrolled programs).
     */
    public function enrolledPrograms()
    {
        return $this->belongsToMany(Program::class, 'user_programs', 'user_id', 'program_id')
                    ->using(UserProgram::class)
                    ->withPivot([
                        'id',
                        'enrollment_date',
                        'progress_percentage',
                        'status'
                    ])
                    ->withTimestamps();
    }

    /**
     * 🔥 NEW: Get the user's session completions.
     */
    public function sessionCompletions()
    {
        return $this->hasMany(SessionCompletion::class, 'user_id');
    }

    /**
     * 🔥 NEW: Get the user's completed sessions.
     */
    public function completedSessions()
    {
        return $this->sessionCompletions()->where('is_completed', true);
    }

    /**
     * 🔥 NEW: Get the user's activity submissions.
     */
    public function activitySubmissions()
    {
        return $this->hasMany(ActivitySubmission::class, 'user_id');
    }

    /**
     * 🔥 NEW: Get the user's submitted activities.
     */
    public function submittedActivities()
    {
        return $this->activitySubmissions()->where('status', '!=', 'pending');
    }

    /**
     * 🔥 NEW: Get the user's completed activities.
     */
    public function completedActivities()
    {
        return $this->activitySubmissions()->whereIn('status', ['completed', 'approved']);
    }

    /**
     * 🔥 NEW: Get the user's pending activities.
     */
    public function pendingActivities()
    {
        return $this->activitySubmissions()->where('status', 'pending');
    }

    /**
     * 🔥 UPDATED: Check if user is enrolled in a specific program.
     */
    public function isEnrolledInProgram($programId): bool
    {
        return $this->userPrograms()
            ->where('program_id', $programId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->exists();
    }

    /**
     * 🔥 UPDATED: Get user program enrollment for a specific program.
     */
    public function getUserProgram($programId)
    {
        return $this->userPrograms()
            ->where('program_id', $programId)
            ->first();
    }

    /**
     * 🔥 UPDATED: Enroll user in a program (using UserProgram model).
     */
    public function enrollInProgram($programId, $data = [])
    {
        // Check if already enrolled
        if ($this->isEnrolledInProgram($programId)) {
            return null;
        }

        $userProgram = UserProgram::create(array_merge([
            'user_id' => $this->id,
            'program_id' => $programId,
            'enrollment_date' => now(),
            'status' => 'enrolled',
            'progress_percentage' => 0,
        ], $data));

        return $userProgram;
    }

    /**
     * 🔥 NEW: Mark a session as completed for this user.
     */
    public function markSessionAsCompleted($sessionId, $completedAt = null)
    {
        return SessionCompletion::updateOrCreate(
            [
                'user_id' => $this->id,
                'session_id' => $sessionId,
            ],
            [
                'is_completed' => true,
                'completed_at' => $completedAt ?? now()
            ]
        );
    }

    /**
     * 🔥 NEW: Submit an activity for this user.
     */
    public function submitActivity($activityId, $data = [])
    {
        return ActivitySubmission::updateOrCreate(
            [
                'user_id' => $this->id,
                'activity_id' => $activityId,
            ],
            array_merge([
                'status' => 'submitted',
                'submission_date' => now(),
            ], $data)
        );
    }

    /**
     * 🔥 NEW: Mark an activity as completed for this user.
     */
    public function markActivityAsCompleted($activityId, $notes = null)
    {
        return ActivitySubmission::updateOrCreate(
            [
                'user_id' => $this->id,
                'activity_id' => $activityId,
            ],
            [
                'status' => 'completed',
                'submission_date' => now(),
                'notes' => $notes
            ]
        );
    }

    /**
     * 🔥 NEW: Check if user has completed a specific session.
     */
    public function hasCompletedSession($sessionId): bool
    {
        return $this->sessionCompletions()
            ->where('session_id', $sessionId)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * 🔥 UPDATED: Check if user has submitted/completed an activity.
     */
    public function hasSubmittedActivity($activityId): bool
    {
        return $this->activitySubmissions()
            ->where('activity_id', $activityId)
            ->where('status', '!=', 'pending')
            ->exists();
    }

    public function hasCompletedActivity($activityId): bool
    {
        return $this->activitySubmissions()
            ->where('activity_id', $activityId)
            ->whereIn('status', ['completed', 'approved'])
            ->exists();
    }

    // ==================== الطرق المساعدة ====================

    /**
     * Get user's program statistics.
     */
    public function getProgramStatistics()
    {
        return [
            'total_programs' => $this->userPrograms()->count(),
            'enrolled_programs' => $this->userPrograms()->whereIn('status', ['enrolled', 'in_progress'])->count(),
            'completed_programs' => $this->userPrograms()->where('status', 'completed')->count(),
            'total_sessions_completed' => $this->completedSessions()->count(),
            'total_activities_completed' => $this->completedActivities()->count(),
            'total_activities_submitted' => $this->submittedActivities()->count(),
            'average_progress' => round($this->userPrograms()->avg('progress_percentage') ?? 0, 2),
            'enrollment_date' => $this->joined_at ? $this->joined_at->format('Y-m-d') : null,
        ];
    }

    /**
     * UPDATED: Get user's completed sessions count.
     */
    public function getCompletedSessionsCount()
    {
        return $this->completedSessions()->count();
    }

    /**
     * UPDATED: Get user's completed activities count.
     */
    public function getCompletedActivitiesCount()
    {
        return $this->completedActivities()->count();
    }

    /**
     * Get user's submitted activities count.
     */
    public function getSubmittedActivitiesCount()
    {
        return $this->submittedActivities()->count();
    }

    /**
     * Get user's pending activities count.
     */
    public function getPendingActivitiesCount()
    {
        return $this->pendingActivities()->count();
    }

    /**
     * UPDATED: Get user's overall progress percentage.
     */
    public function getOverallProgress()
    {
        $totalPrograms = $this->userPrograms()->count();
        $averageProgress = $this->userPrograms()->avg('progress_percentage') ?? 0;

        return round($averageProgress, 2);
    }

    /**
     * Get user's progress in a specific program.
     */
    public function getProgramProgress($programId)
    {
        $userProgram = $this->getUserProgram($programId);
        return $userProgram ? $userProgram->progress_percentage : 0;
    }

    /**
     * Get user's status in a specific program.
     */
    public function getProgramStatus($programId)
    {
        $userProgram = $this->getUserProgram($programId);
        return $userProgram ? $userProgram->status : null;
    }

    /**
     * Get user's avatar URL with fallback.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        // صورة افتراضية حسب الجنس
        $defaultAvatar = $this->gender === 'female'
            ? 'defaults/avatar-female.png'
            : 'defaults/avatar-male.png';

        return asset('storage/' . $defaultAvatar);
    }

    /**
     * Get user's display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?? $this->email;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'Admin';
    }

    /**
     * Check if user is therapist.
     */
    public function isTherapist(): bool
    {
        return $this->role === 'therapist' || $this->role === 'Therapist';
    }

    /**
     * Check if user is client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client' || $this->role === 'Client';
    }

    /**
     * Check if user is patient.
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient' || $this->role === 'Patient';
    }

    /**
     * Check if user can create programs.
     */
    public function canCreatePrograms(): bool
    {
        return $this->isAdmin() || $this->isTherapist();
    }

    /**
     * Check if user can assign sessions.
     */
    public function canAssignSessions(): bool
    {
        return $this->isAdmin() || $this->isTherapist();
    }

    /**
     * Check if user can view all programs.
     */
    public function canViewAllPrograms(): bool
    {
        return $this->isAdmin() || $this->isTherapist();
    }

    // ==================== Scopes ====================

    /**
     * Scope for admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'Admin');
    }

    /**
     * Scope for therapist users.
     */
    public function scopeTherapists($query)
    {
        return $query->where('role', 'Therapist');
    }

    /**
     * Scope for client users.
     */
    public function scopeClients($query)
    {
        return $query->where('role', 'Client');
    }

    /**
     * Scope for patient users.
     */
    public function scopePatients($query)
    {
        return $query->where('role', 'Patient');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('joined_at');
    }

    /**
     * Get users enrolled in a specific program.
     */
    public function scopeEnrolledInProgram($query, $programId)
    {
        return $query->whereHas('userPrograms', function ($q) use ($programId) {
            $q->where('program_id', $programId);
        });
    }

    /**
     * Get users who completed a specific program.
     */
    public function scopeCompletedProgram($query, $programId)
    {
        return $query->whereHas('userPrograms', function ($q) use ($programId) {
            $q->where('program_id', $programId)
              ->where('status', 'completed');
        });
    }

    /**
     * Get users who completed a specific session.
     */
    public function scopeCompletedSession($query, $sessionId)
    {
        return $query->whereHas('sessionCompletions', function ($q) use ($sessionId) {
            $q->where('session_id', $sessionId)
              ->where('is_completed', true);
        });
    }

    /**
     * Get users who submitted a specific activity.
     */
    public function scopeSubmittedActivity($query, $activityId)
    {
        return $query->whereHas('activitySubmissions', function ($q) use ($activityId) {
            $q->where('activity_id', $activityId)
              ->where('status', '!=', 'pending');
        });
    }

    /**
     * Get users with specific program status.
     */
    public function scopeWithProgramStatus($query, $programId, $status)
    {
        return $query->whereHas('userPrograms', function ($q) use ($programId, $status) {
            $q->where('program_id', $programId)
              ->where('status', $status);
        });
    }

    // ==================== Statistics ====================

    /**
     * Get detailed program statistics for user.
     */
    public function programs_statistics()
    {
        $totalEnrolled = $this->userPrograms()
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->count();

        $totalCompleted = $this->userPrograms()
            ->where('status', 'completed')
            ->count();

        $totalInProgress = $this->userPrograms()
            ->where('status', 'in_progress')
            ->count();

        $totalEnrolledStatus = $this->userPrograms()
            ->where('status', 'enrolled')
            ->count();

        $averageProgress = $this->userPrograms()->avg('progress_percentage') ?? 0;

        return [
            'total_enrolled' => $totalEnrolled,
            'total_completed' => $totalCompleted,
            'total_in_progress' => $totalInProgress,
            'total_enrolled_status' => $totalEnrolledStatus,
            'total_programs' => $this->userPrograms()->count(),
            'average_progress' => round($averageProgress, 2),
            'total_sessions_completed' => $this->getCompletedSessionsCount(),
            'total_activities_completed' => $this->getCompletedActivitiesCount(),
            'total_activities_submitted' => $this->getSubmittedActivitiesCount(),
            'total_activities_pending' => $this->getPendingActivitiesCount(),
        ];
    }

    /**
     * Get recent program activities for user.
     */
    public function getRecentActivities($limit = 10)
    {
        $recentSessions = $this->sessionCompletions()
            ->with('session')
            ->where('is_completed', true)
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();

        $recentActivities = $this->activitySubmissions()
            ->with('activity')
            ->where('status', '!=', 'pending')
            ->orderBy('submission_date', 'desc')
            ->limit($limit)
            ->get();

        return [
            'recent_sessions' => $recentSessions,
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * Get user's learning timeline.
     */
    public function getLearningTimeline()
    {
        $timeline = collect();

        // Add program enrollments
        $enrollments = $this->userPrograms()
            ->with('program')
            ->orderBy('enrollment_date', 'desc')
            ->get()
            ->map(function($userProgram) {
                return [
                    'type' => 'program_enrollment',
                    'title' => 'تم التسجيل في برنامج ' . $userProgram->program->name,
                    'date' => $userProgram->enrollment_date,
                    'data' => $userProgram
                ];
            });

        // Add session completions
        $sessions = $this->sessionCompletions()
            ->with('session')
            ->where('is_completed', true)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function($completion) {
                return [
                    'type' => 'session_completion',
                    'title' => 'تم إكمال جلسة ' . $completion->session->title,
                    'date' => $completion->completed_at,
                    'data' => $completion
                ];
            });

        // Add activity submissions
        $activities = $this->activitySubmissions()
            ->with('activity')
            ->where('status', '!=', 'pending')
            ->orderBy('submission_date', 'desc')
            ->get()
            ->map(function($submission) {
                return [
                    'type' => 'activity_submission',
                    'title' => 'تم تسليم نشاط ' . $submission->activity->name,
                    'date' => $submission->submission_date,
                    'data' => $submission
                ];
            });

        $timeline = $timeline->merge($enrollments)
                            ->merge($sessions)
                            ->merge($activities)
                            ->sortByDesc('date')
                            ->values();

        return $timeline;
    }

    // public function getAvatarUrlAttribute()
    // {
    //     if ($this->avatar) {
    //         return Storage::disk('public')->url($this->avatar);
    //     }
    //     return null;
    // }
}
