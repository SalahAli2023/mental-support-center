<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'program_id',
        'phase_id',
        'title_ar',
        'title_en',
        'session_order',
        'goal_ar',
        'goal_en',
        'duration',
        'is_active',
    ];

    protected $appends = ['activities_count'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ================= العلاقات =================
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function phase()
    {
        return $this->belongsTo(ProgramPhase::class, 'phase_id');
    }

    public function activities()
    {
        return $this->hasMany(SessionActivity::class, 'session_id')
                    ->orderBy('activity_order');
    }

    public function activeActivities()
    {
        return $this->activities()->where('is_active', true);
    }

    public function homework()
    {
        return $this->hasMany(SessionHomework::class, 'session_id')
                    ->orderBy('homework_order');
    }

    public function mandatoryHomework()
    {
        return $this->homework()->where('is_mandatory', true);
    }

    public function mandatoryActivities()
    {
        return $this->activities()->where('is_mandatory', true);
    }

    public function optionalActivities()
    {
        return $this->activities()->where('is_mandatory', false);
    }

    // العلاقات مع الجداول الجديدة
    public function sessionCompletions()
    {
        return $this->hasMany(SessionCompletion::class, 'session_id');
    }

    public function completedSessions()
    {
        return $this->sessionCompletions()->where('is_completed', true);
    }

    public function pendingSessions()
    {
        return $this->sessionCompletions()->where('is_completed', false);
    }

    public function activitySubmissions()
    {
        return $this->hasManyThrough(
            ActivitySubmission::class,
            SessionActivity::class,
            'session_id', // Foreign key on SessionActivity table
            'activity_id', // Foreign key on ActivitySubmission table
            'id', // Local key on ProgramSession table
            'id' // Local key on SessionActivity table
        );
    }

    // العلاقة مع المستخدمين من خلال إكمال الجلسات
    public function users()
    {
        return $this->belongsToMany(User::class, 'session_completions', 'session_id', 'user_id')
                    ->withPivot(['is_completed', 'completed_at'])
                    ->withTimestamps();
    }

    // العلاقة مع المستخدمين الذين أكملوا الجلسة
    public function completedUsers()
    {
        return $this->belongsToMany(User::class, 'session_completions', 'session_id', 'user_id')
                    ->wherePivot('is_completed', true)
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    // ================= Accessors =================
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getGoalAttribute()
    {
        if (app()->getLocale() === 'ar') {
            return $this->goal_ar ?: $this->goal_en;
        }
        return $this->goal_en ?: $this->goal_ar;
    }

    public function getActivitiesCountAttribute()
    {
        return $this->activities()->count();
    }

    public function getMandatoryActivitiesCountAttribute()
    {
        return $this->mandatoryActivities()->count();
    }

    public function getOptionalActivitiesCountAttribute()
    {
        return $this->optionalActivities()->count();
    }

    public function getDurationLabelAttribute()
    {
        return $this->duration . ' دقيقة';
    }

    public function getSessionOrderLabelAttribute()
    {
        return 'الجلسة ' . $this->session_order;
    }

    public function getCompletionRateAttribute()
    {
        $totalUsers = $this->program->enrolled_users_count ?? 0;
        $completedUsers = $this->completedUsers()->count();
        
        return $totalUsers > 0 ? round(($completedUsers / $totalUsers) * 100, 2) : 0;
    }

    public function getIsCompletedByUserAttribute()
    {
        if (!auth()->check()) return false;
        
        return $this->sessionCompletions()
                    ->where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->exists();
    }

    public function getUserCompletionAttribute()
    {
        if (!auth()->check()) return null;
        
        return $this->sessionCompletions()
                    ->where('user_id', auth()->id())
                    ->first();
    }

    // ================= Scopes =================
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('session_order');
    }

    public function scopeWithActivities($query)
    {
        return $query->with(['activities' => function($q) {
            $q->orderBy('created_at');
        }]);
    }

    public function scopeWithCompletionStats($query)
    {
        return $query->withCount([
            'sessionCompletions',
            'sessionCompletions as completed_sessions_count' => function($q) {
                $q->where('is_completed', true);
            }
        ]);
    }

    public function scopeByUserCompletion($query, $userId, $completed = true)
    {
        return $query->whereHas('sessionCompletions', function($q) use ($userId, $completed) {
            $q->where('user_id', $userId)
              ->where('is_completed', $completed);
        });
    }

    // ================= Methods =================
    public function markAsCompleted($userId, $completedAt = null)
    {
        return $this->sessionCompletions()->updateOrCreate(
            ['user_id' => $userId],
            [
                'is_completed' => true,
                'completed_at' => $completedAt ?? now()
            ]
        );
    }

    public function markAsPending($userId)
    {
        return $this->sessionCompletions()->updateOrCreate(
            ['user_id' => $userId],
            ['is_completed' => false, 'completed_at' => null]
        );
    }

    public function getUserProgress($userId)
    {
        $completedActivities = $this->activitySubmissions()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
            
        $totalActivities = $this->activities_count;
        
        return $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0;
    }

    public function isUserCompleted($userId)
    {
        return $this->sessionCompletions()
                    ->where('user_id', $userId)
                    ->where('is_completed', true)
                    ->exists();
    }
}