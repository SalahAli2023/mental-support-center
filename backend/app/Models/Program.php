<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'target_category_ar',
        'target_category_en',
        'duration',
        'max_duration_days',
        'session_duration_minutes',
        'session_gap_hours',
        'activity_gap_hours',
        'status',
        'scale_id',
        'image_url',
    ];

    protected $casts = [
        'duration' => 'integer',
        'max_duration_days' => 'integer',
        'session_duration_minutes' => 'integer',
        'session_gap_hours' => 'integer',
        'activity_gap_hours' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    public function scale()
    {
        return $this->belongsTo(PsychologicalScale::class, 'scale_id');
    }

    public function sessions()
    {
        return $this->hasMany(ProgramSession::class, 'program_id')
                    ->orderBy('session_order');
    }

    // العلاقات مع الجداول الجديدة
    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class, 'program_id');
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'user_programs', 'program_id', 'user_id')
                    ->withPivot(['enrollment_date', 'progress_percentage', 'status'])
                    ->withTimestamps();
    }

    public function sessionCompletions()
    {
        return $this->hasManyThrough(
            SessionCompletion::class,
            ProgramSession::class,
            'program_id', // Foreign key on ProgramSession table
            'session_id', // Foreign key on SessionCompletion table
            'id', // Local key on Program table
            'id' // Local key on ProgramSession table
        );
    }

    public function activitySubmissions()
    {
        return $this->hasManyThrough(
            ActivitySubmission::class,
            SessionActivity::class,
            'program_id', // Foreign key on SessionActivity table
            'activity_id', // Foreign key on ActivitySubmission table
            'id', // Local key on Program table
            'id' // Local key on SessionActivity table
        );
    }

    // ================= Accessors =================
    public function getSessionsCountAttribute()
    {
        return $this->sessions()->count();
    }

    public function getEnrolledUsersCountAttribute()
    {
        return $this->userPrograms()->count();
    }

    public function getCompletedUsersCountAttribute()
    {
        return $this->userPrograms()->where('status', 'completed')->count();
    }

    public function getActiveUsersCountAttribute()
    {
        return $this->userPrograms()->whereIn('status', ['enrolled', 'in_progress'])->count();
    }

    public function getAverageProgressAttribute()
    {
        return $this->userPrograms()->avg('progress_percentage') ?? 0;
    }

    public function getCompletionRateAttribute()
    {
        $total = $this->enrolled_users_count;
        $completed = $this->completed_users_count;
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getTargetCategoryAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->target_category_ar : $this->target_category_en;
    }

    public function getDurationInHoursAttribute()
    {
        return $this->duration;
    }

    public function getDurationInWeeksAttribute()
    {
        return ceil($this->duration / 40); // Assuming 40 hours per week
    }

    public function getDurationInMonthsAttribute()
    {
        return ceil($this->duration / 160); // Assuming 160 hours per month
    }

    // ================= Scopes =================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByScale($query, $scaleId)
    {
        return $query->where('scale_id', $scaleId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where(function($q) use ($category) {
            $q->where('target_category_ar', 'LIKE', "%{$category}%")
              ->orWhere('target_category_en', 'LIKE', "%{$category}%");
        });
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name_ar', 'LIKE', "%{$searchTerm}%")
              ->orWhere('name_en', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description_ar', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description_en', 'LIKE', "%{$searchTerm}%");
        });
    }

    public function scopeWithEnrollments($query)
    {
        return $query->withCount('userPrograms');
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('userPrograms')
                    ->orderBy('user_programs_count', 'desc')
                    ->limit($limit);
    }

    // ================= Methods =================
    public function enrollUser($userId, $data = [])
    {
        return $this->userPrograms()->create(array_merge([
            'user_id' => $userId,
            'enrollment_date' => now(),
            'status' => 'enrolled',
            'progress_percentage' => 0,
        ], $data));
    }

    public function isUserEnrolled($userId)
    {
        return $this->userPrograms()->where('user_id', $userId)->exists();
    }

    public function getUserProgress($userId)
    {
        $userProgram = $this->userPrograms()->where('user_id', $userId)->first();
        return $userProgram ? $userProgram->progress_percentage : null;
    }

    public function getUserStatus($userId)
    {
        $userProgram = $this->userPrograms()->where('user_id', $userId)->first();
        return $userProgram ? $userProgram->status : null;
    }
}