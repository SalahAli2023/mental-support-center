<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name, // الاسم الحالي بناءً على اللغة
            'activity_type' => $this->activity_type,
            'activity_type_label' => $this->activity_type_label,
            'instructions_ar' => $this->instructions_ar,
            'instructions_en' => $this->instructions_en,
            'instructions' => $this->instructions, // التعليمات الحالية بناءً على اللغة
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'media_url' => $this->media_url,
            'media_type' => $this->media_type,
            'duration_minutes' => $this->duration_minutes,
            'activity_config' => $this->activity_config,
            'activity_order' => $this->activity_order,
            'is_active' => (bool) $this->is_active,
            'scale_id' => $this->scale_id,
            'is_mandatory' => (bool) $this->is_mandatory,
            'mandatory_label' => $this->mandatory_label,
            
            // العلاقات
            'session' => $this->whenLoaded('session', function () {
                return [
                    'id' => $this->session->id,
                    'title_ar' => $this->session->title_ar,
                    'title_en' => $this->session->title_en,
                    'title' => $this->session->title,
                    'session_order' => $this->session->session_order,
                ];
            }),
            
            // التواريخ
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('d/m/Y'),
        ];
    }
}