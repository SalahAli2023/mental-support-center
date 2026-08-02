<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'option_text_ar' => $this->option_text_ar,
            'option_text_en' => $this->option_text_en,
            'score_value' => $this->score_value,
            'option_order' => $this->option_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}