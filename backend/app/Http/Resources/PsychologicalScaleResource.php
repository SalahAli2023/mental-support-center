<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PsychologicalScaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'image_url' => $this->formatImageUrl($this->image_url),
            'max_score' => $this->max_score,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'questions' => ScaleQuestionResource::collection($this->whenLoaded('questions')),
            'interpretations' => ResultInterpretationResource::collection($this->whenLoaded('interpretations')),
            'questions_count' => $this->whenCounted('questions'),
        ];
    }
    private function formatImageUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', 'data:image'])) {
            return $value;
        }

        if (Str::startsWith($value, 'storage/')) {
            return asset($value);
        }

        if (Str::startsWith($value, 'public/')) {
            $relative = Str::after($value, 'public/');
            return asset('storage/' . ltrim($relative, '/'));
        }

        if (Storage::disk('public')->exists($value)) {
            return asset('storage/' . ltrim($value, '/'));
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}