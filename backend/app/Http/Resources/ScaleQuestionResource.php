<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScaleQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'scale_id' => $this->scale_id,
            'question_text_ar' => $this->question_text_ar,
            'question_text_en' => $this->question_text_en,
            'question_order' => $this->question_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'options' => QuestionOptionResource::collection($this->whenLoaded('options')),
            'options_count' => $this->whenCounted('options'),
        ];
    }
}