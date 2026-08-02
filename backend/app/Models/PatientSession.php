<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'therapist_id',
        'title_ar',
        'title_en',
        'session_date',
        'session_time',
        'status',
        'progress',
        'type',
        'location',
        'notes_ar',
        'notes_en',
        'report_ar',
        'report_en',
        'attachments',
        'duration'
    ];

    protected $casts = [
        'session_date' => 'date',
        'attachments' => 'array'
    ];

    /**
     * العلاقة مع المريض
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * العلاقة مع المعالج
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }

    /**
     * الحصول على العنوان بناءً على اللغة
     */
    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * الحصول على الملاحظات بناءً على اللغة
     */
    public function getNotesAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->notes_ar : $this->notes_en;
    }

    /**
     * الحصول على التقرير بناءً على اللغة
     */
    public function getReportAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->report_ar : $this->report_en;
    }

    /**
     * التحقق إذا كانت الجلسة مجدولة
     */
    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * التحقق إذا كانت الجلسة مكتملة
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * التحقق إذا كانت الجلسة ملغاة
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * الحصول على التاريخ والوقت معاً
     */
    public function getFullDateTimeAttribute(): string
    {
        return $this->session_date->format('Y-m-d') . ' ' . $this->session_time;
    }

    /**
     * الحصول على وقت نهاية الجلسة
     */
    public function getEndTimeAttribute(): string
    {
        $duration = $this->duration ?? 60; // القيمة الافتراضية 60 دقيقة
        return date('H:i', strtotime("+{$duration} minutes", strtotime($this->session_time)));
    }

    /**
     * الحصول على حالة الجلسة كنص
     */
    public function getStatusTextAttribute(): string
    {
        $statusMap = [
            'scheduled' => 'مجدولة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * الحصول على نوع الجلسة كنص
     */
    public function getTypeTextAttribute(): string
    {
        $typeMap = [
            'individual' => 'فردية',
            'group' => 'جماعية',
            'family' => 'عائلية',
            'assessment' => 'تقييم',
            'followup' => 'متابعة'
        ];

        return $typeMap[$this->type] ?? $this->type;
    }

    /**
     * الحصول على موقع الجلسة كنص
     */
    public function getLocationTextAttribute(): string
    {
        $locationMap = [
            'clinic' => 'العيادة',
            'online' => 'أونلاين',
            'home' => 'منزلية'
        ];

        return $locationMap[$this->location] ?? $this->location;
    }

    /**
     * نطاق للجلسات المجدولة
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * نطاق للجلسات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * نطاق للجلسات الملغاة
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * نطاق للجلسات القادمة
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('session_date', '>=', now()->format('Y-m-d'));
    }

    /**
     * نطاق للجلسات السابقة
     */
    public function scopePast($query)
    {
        return $query->where(function($q) {
            $q->where('session_date', '<', now()->format('Y-m-d'))
              ->orWhere(function($q) {
                  $q->where('session_date', now()->format('Y-m-d'))
                    ->where('session_time', '<', now()->format('H:i'));
              });
        });
    }

    /**
     * نطاق للبحث في الجلسات
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title_ar', 'like', "%{$search}%")
              ->orWhere('title_en', 'like', "%{$search}%")
              ->orWhere('notes_ar', 'like', "%{$search}%")
              ->orWhere('notes_en', 'like', "%{$search}%")
              ->orWhere('report_ar', 'like', "%{$search}%")
              ->orWhere('report_en', 'like', "%{$search}%")
              ->orWhereHas('therapist', function($q) use ($search) {
                  $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
              });
        });
    }

    /**
     * نطاق للجلسات حسب النوع
     */
    public function scopeOfType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * نطاق للجلسات حسب الموقع
     */
    public function scopeOfLocation($query, $location)
    {
        if ($location) {
            return $query->where('location', $location);
        }
        return $query;
    }

    /**
     * نطاق للجلسات حسب المعالج
     */
    public function scopeOfTherapist($query, $therapistId)
    {
        if ($therapistId) {
            return $query->where('therapist_id', $therapistId);
        }
        return $query;
    }

    /**
     * التحقق من توفر الجلسة للتعديل
     */
    public function getCanBeModifiedAttribute(): bool
    {
        return $this->is_scheduled && 
               $this->session_date >= now()->format('Y-m-d');
    }

    /**
     * التحقق إذا كانت الجلسة نشطة الآن
     */
    public function getIsActiveNowAttribute(): bool
    {
        if (!$this->is_scheduled) {
            return false;
        }

        $sessionDateTime = $this->session_date->format('Y-m-d') . ' ' . $this->session_time;
        $sessionStart = strtotime($sessionDateTime);
        $sessionEnd = strtotime($this->end_time);
        $now = time();

        return $now >= $sessionStart && $now <= $sessionEnd;
    }

    /**
     * الحصول على الوقت المتبقي للجلسة
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->is_scheduled) {
            return null;
        }

        $sessionDateTime = $this->session_date->format('Y-m-d') . ' ' . $this->session_time;
        $sessionTime = strtotime($sessionDateTime);
        $now = time();
        $diff = $sessionTime - $now;

        if ($diff <= 0) {
            return 'بدأت';
        }

        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);

        if ($hours > 0) {
            return "{$hours} ساعة و {$minutes} دقيقة";
        } else {
            return "{$minutes} دقيقة";
        }
    }

    /**
     * تنسيق بيانات الجلسة للاستجابة API
     */
    public function formatForApi(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'session_date' => $this->session_date->format('Y-m-d'),
            'session_time' => $this->session_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'progress' => $this->progress,
            'type' => $this->type,
            'type_text' => $this->type_text,
            'location' => $this->location,
            'location_text' => $this->location_text,
            'notes' => $this->notes,
            'notes_ar' => $this->notes_ar,
            'notes_en' => $this->notes_en,
            'report' => $this->report,
            'report_ar' => $this->report_ar,
            'report_en' => $this->report_en,
            'duration' => $this->duration,
            'attachments' => $this->attachments ?? [],
            'can_be_modified' => $this->can_be_modified,
            'is_active_now' => $this->is_active_now,
            'time_remaining' => $this->time_remaining,
            'full_date_time' => $this->full_date_time,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'patient' => $this->patient ? [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name ?? '',
                'email' => $this->patient->user->email ?? '',
                'phone' => $this->patient->user->phone ?? ''
            ] : null,
            'therapist' => $this->therapist ? [
                'id' => $this->therapist->id,
                'name' => $this->therapist->name_ar ?? $this->therapist->name_en,
                'specialty' => $this->therapist->specialty_ar ?? $this->therapist->specialty_en,
                'session_duration' => $this->therapist->session_duration,
                'experience' => $this->therapist->experience,
                'rating' => $this->therapist->rating
            ] : null
        ];
    }
}