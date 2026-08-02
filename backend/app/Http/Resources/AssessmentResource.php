<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'scale_id' => $this->scale_id,
            'total_score' => $this->total_score,
            'interpretation_level' => $this->interpretation_level,
            'assessment_data' => $this->assessment_data,
            'psychological_scale' => $this->when(
                $this->relationLoaded('psychologicalScale') && $this->psychologicalScale,
                new PsychologicalScaleResource($this->psychologicalScale)
            ),
            'user' => $this->when(
                $this->relationLoaded('user') && $this->user,
                [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ]
            ),
            'completed_at' => $this->completed_at?->toDateTimeString() ?? $this->created_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
