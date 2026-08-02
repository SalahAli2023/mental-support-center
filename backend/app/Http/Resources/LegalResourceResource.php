<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale') ?? $request->header('Accept-Language', 'en');
        $locale = in_array($locale, ['ar', 'en']) ? $locale : 'en';

        return [
            'id' => $this->id,
            'law_type' => $this->law_type,
            'article_number' => $locale === 'ar' ? $this->article_number_ar : $this->article_number_en,
            'article_number_ar' => $this->article_number_ar,
            'article_number_en' => $this->article_number_en,
            'text' => $locale === 'ar' ? $this->text_ar : $this->text_en,
            'text_ar' => $this->text_ar,
            'text_en' => $this->text_en,
            'category' => new LegalResourceCategoryResource($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
