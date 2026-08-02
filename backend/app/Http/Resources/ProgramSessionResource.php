<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title' => $this->title, // العنوان الحالي بناءً على اللغة
            'session_order' => $this->session_order,
            'goal_ar' => $this->goal_ar,
            'goal_en' => $this->goal_en,
            'goal' => $this->goal, // الهدف الحالي بناءً على اللغة
            'duration' => $this->duration,
            'duration_label' => $this->duration_label,
            
            // الإحصائيات
            'activities_count' => $this->whenLoaded('activities', $this->activities->count()),
            'mandatory_activities_count' => $this->whenLoaded('activities', function() {
                return $this->activities->where('is_mandatory', true)->count();
            }),
            
            // العلاقات
            'program' => $this->whenLoaded('program', function () {
                return [
                    'id' => $this->program->id,
                    'name_ar' => $this->program->name_ar,
                    'name_en' => $this->program->name_en,
                    'name' => $this->program->name,
                ];
            }),
            
            'activities' => $this->whenLoaded('activities'),
            
            // التواريخ
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('d/m/Y'),
        ];
    }
}