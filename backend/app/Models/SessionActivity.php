<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'session_id',
        'name_ar',
        'name_en',
        'activity_type',
        'content_ar',
        'content_en',
        'media_url',
        'media_type',
        'duration_minutes',
        'activity_config',
        'activity_order',
        'is_active',
        'scale_id',
        'instructions_ar',
        'instructions_en',
        'is_mandatory',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'activity_order' => 'integer',
        'activity_config' => 'array',
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
    public function session()
    {
        return $this->belongsTo(ProgramSession::class, 'session_id');
    }

    // العلاقات مع الجداول الجديدة
    public function activitySubmissions()
    {
        return $this->hasMany(ActivitySubmission::class, 'activity_id');
    }

    public function submissions()
    {
        return $this->activitySubmissions();
    }

    public function submittedActivities()
    {
        return $this->activitySubmissions()->where('status', '!=', 'pending');
    }

    public function completedActivities()
    {
        return $this->activitySubmissions()->whereIn('status', ['completed', 'approved']);
    }

    public function pendingActivities()
    {
        return $this->activitySubmissions()->where('status', 'pending');
    }

    // العلاقة مع المستخدمين من خلال تسليم الأنشطة
    public function users()
    {
        return $this->belongsToMany(User::class, 'activity_submissions', 'activity_id', 'user_id')
                    ->withPivot(['status', 'submission_date', 'notes'])
                    ->withTimestamps();
    }

    public function submittedUsers()
    {
        return $this->belongsToMany(User::class, 'activity_submissions', 'activity_id', 'user_id')
                    ->wherePivot('status', '!=', 'pending')
                    ->withPivot(['submission_date', 'notes'])
                    ->withTimestamps();
    }

    public function completedUsers()
    {
        return $this->belongsToMany(User::class, 'activity_submissions', 'activity_id', 'user_id')
                    ->wherePivotIn('status', ['completed', 'approved'])
                    ->withPivot(['submission_date', 'notes'])
                    ->withTimestamps();
    }

    // ================= Accessors =================
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getInstructionsAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->instructions_ar : $this->instructions_en;
    }

    public function getSubmissionsCountAttribute()
    {
        return $this->activitySubmissions()->count();
    }

    public function getCompletedSubmissionsCountAttribute()
    {
        return $this->completedActivities()->count();
    }

    public function getPendingSubmissionsCountAttribute()
    {
        return $this->pendingActivities()->count();
    }

    public function getCompletionRateAttribute()
    {
        $totalUsers = $this->session->program->enrolled_users_count ?? 0;
        $completedUsers = $this->completedUsers()->count();
        
        return $totalUsers > 0 ? round(($completedUsers / $totalUsers) * 100, 2) : 0;
    }

    public function getMandatoryLabelAttribute()
    {
        return $this->is_mandatory ? 'إلزامي' : 'اختياري';
    }

    public function getIsSubmittedByUserAttribute()
    {
        if (!auth()->check()) return false;
        
        return $this->activitySubmissions()
                    ->where('user_id', auth()->id())
                    ->where('status', '!=', 'pending')
                    ->exists();
    }

    public function getIsCompletedByUserAttribute()
    {
        if (!auth()->check()) return false;
        
        return $this->activitySubmissions()
                    ->where('user_id', auth()->id())
                    ->whereIn('status', ['completed', 'approved'])
                    ->exists();
    }

    public function getUserSubmissionAttribute()
    {
        if (!auth()->check()) return null;
        
        return $this->activitySubmissions()
                    ->where('user_id', auth()->id())
                    ->first();
    }

    // ================= Scopes =================
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    public function scopeWithSubmissions($query)
    {
        return $query->with(['activitySubmissions' => function($q) {
            $q->orderBy('submission_date', 'desc');
        }]);
    }

    public function scopeByUserStatus($query, $userId, $status = null)
    {
        return $query->whereHas('activitySubmissions', function($q) use ($userId, $status) {
            $q->where('user_id', $userId);
            if ($status) {
                $q->where('status', $status);
            }
        });
    }

    // ================= Methods =================
    public function submitActivity($userId, $data = [])
    {
        return $this->activitySubmissions()->updateOrCreate(
            ['user_id' => $userId],
            array_merge([
                'status' => 'submitted',
                'submission_date' => now(),
            ], $data)
        );
    }

    public function markAsCompleted($userId, $notes = null)
    {
        return $this->activitySubmissions()->updateOrCreate(
            ['user_id' => $userId],
            [
                'status' => 'completed',
                'submission_date' => now(),
                'notes' => $notes
            ]
        );
    }

    public function isUserSubmitted($userId)
    {
        return $this->activitySubmissions()
                    ->where('user_id', $userId)
                    ->where('status', '!=', 'pending')
                    ->exists();
    }

    public function isUserCompleted($userId)
    {
        return $this->activitySubmissions()
                    ->where('user_id', $userId)
                    ->whereIn('status', ['completed', 'approved'])
                    ->exists();
    }

    public function getUserStatus($userId)
    {
        $submission = $this->activitySubmissions()
                          ->where('user_id', $userId)
                          ->first();
                          
        return $submission ? $submission->status : 'pending';
    }
}