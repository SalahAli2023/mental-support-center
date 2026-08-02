<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Therapist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name_ar',
        'name_en',
        'title_ar',
        'title_en',
        'methodologies_ar',
        'methodologies_en',
        'specialty_ar',
        'specialty_en',
        'session_duration',
        'experience',
        'total_sessions',
        'hourly_rate',
        'phone',
        'date_of_birth',
        'gender',
        'rating',
        'rating_count',
        'bio_ar',
        'bio_en',
        'status'
    ];

    protected $casts = [
        'methodologies_ar' => 'array',
        'methodologies_en' => 'array',
        'date_of_birth' => 'date',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع المؤهلات
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(TherapistQualification::class);
    }

    /**
     * العلاقة مع الشهادات
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(TherapistCertification::class);
    }

    /**
     * العلاقة مع الجداول الزمنية
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TherapistSchedule::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TherapistReview::class);
    }
}