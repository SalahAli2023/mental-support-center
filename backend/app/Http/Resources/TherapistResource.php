<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\MediaHelper;
use App\Http\Resources\TherapistReviewResource;

class TherapistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'methodologies_ar' => $this->methodologies_ar,
            'methodologies_en' => $this->methodologies_en,
            'specialty_ar' => $this->specialty_ar,
            'specialty_en' => $this->specialty_en,
            'session_duration' => $this->session_duration,
            'experience' => $this->experience,
            'total_sessions' => $this->total_sessions,
            'hourly_rate' => $this->hourly_rate,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'rating' => $this->rating ? (float) $this->rating : null,
            'rating_count' => (int) ($this->rating_count ?? 0),
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'status' => $this->status,
            'email' => $this->user->email ?? null, // استخدام null coalescing
            'avatar' => $this->getAvatarUrl(), // صورة المعالج من جدول users مع URL كامل
            'user' => $this->whenLoaded('user') ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->getAvatarUrl(),
                'phone' => $this->user->phone,
            ] : null, // تضمين بيانات المستخدم فقط إذا كانت محملة
            'qualifications' => $this->getQualifications(),
            'certifications' => TherapistCertificationResource::collection($this->whenLoaded('certifications')),
            'schedules' => $this->getSchedules(),
            'reviews_summary' => [
                'average' => $this->rating ? (float) $this->rating : null,
                'count' => (int) ($this->rating_count ?? ($this->relationLoaded('reviews') ? $this->reviews->count() : 0)),
            ],
            'reviews' => TherapistReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * الحصول على URL الصورة
     */
    private function getAvatarUrl(): ?string
    {
        if (!$this->user || !$this->user->avatar) {
            return null;
        }

        return MediaHelper::toPublicUrl($this->user->avatar);
    }

    /**
     * الحصول على المؤهلات العلمية
     */
    private function getQualifications()
    {
        // التأكد من تحميل qualifications
        if (!$this->relationLoaded('qualifications')) {
            $this->load('qualifications');
        }
        
        // تسجيل للتحقق من تحميل qualifications
        \Log::info('Qualifications in TherapistResource', [
            'therapist_id' => $this->id,
            'qualifications_count' => $this->qualifications->count(),
            'qualifications_loaded' => $this->relationLoaded('qualifications'),
            'qualifications_data' => $this->qualifications->map(function ($qualification) {
                return [
                    'id' => $qualification->id,
                    'name_ar' => $qualification->name_ar,
                    'name_en' => $qualification->name_en,
                    'institution_ar' => $qualification->institution_ar,
                    'institution_en' => $qualification->institution_en,
                    'year' => $qualification->year
                ];
            })->toArray()
        ]);
        
        return TherapistQualificationResource::collection($this->qualifications ?? collect([]));
    }

    /**
     * الحصول على الجداول الزمنية
     */
    private function getSchedules()
    {
        // التأكد من تحميل schedules
        if (!$this->relationLoaded('schedules')) {
            $this->load('schedules');
        }
        
        // تسجيل للتحقق من تحميل schedules في Resource
        \Log::info('Schedules in TherapistResource', [
            'therapist_id' => $this->id,
            'schedules_count' => $this->schedules->count(),
            'schedules_loaded' => $this->relationLoaded('schedules'),
            'schedules_data' => $this->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'available' => $schedule->available
                ];
            })->toArray()
        ]);
        
        return TherapistScheduleResource::collection($this->schedules ?? collect([]));
    }
}