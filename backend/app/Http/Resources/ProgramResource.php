<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name, // الاسم الحالي بناءً على اللغة
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'description' => $this->description, // الوصف الحالي
            'target_category_ar' => $this->target_category_ar,
            'target_category_en' => $this->target_category_en,
            'target_category' => $this->target_category, // الفئة الحالية
            'duration' => $this->duration,
            'max_duration_days' => $this->max_duration_days,
            'session_duration_minutes' => $this->session_duration_minutes,
            'session_gap_hours' => $this->session_gap_hours,
            'activity_gap_hours' => $this->activity_gap_hours,
            'duration_label' => $this->duration_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'scale_id' => $this->scale_id,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('d/m/Y'),
            
            // الإحصائيات
            'sessions_count' => $this->whenLoaded('sessions', $this->sessions->count()),
            'total_duration' => $this->whenLoaded('sessions', function() {
                $total = $this->sessions->sum('duration');
                return $total ? round($total / 60, 1) . ' ساعة' : 'غير محدد';
            }),
            
            // العلاقات
            'scale' => $this->whenLoaded('scale'),
            'sessions' => $this->whenLoaded('sessions'),
        ];
    }
}