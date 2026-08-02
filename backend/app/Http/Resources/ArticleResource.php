<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PsychologicalScaleResource;
use App\Support\MediaHelper;

class ArticleResource extends JsonResource
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
            'title' => $locale === 'ar' ? $this->title_ar : $this->title_en,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'excerpt' => $locale === 'ar' ? $this->excerpt_ar : $this->excerpt_en,
            'excerpt_ar' => $this->excerpt_ar,
            'excerpt_en' => $this->excerpt_en,
            'content' => $locale === 'ar' ? $this->content_ar : $this->content_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'introduction' => $locale === 'ar' ? $this->introduction_ar : $this->introduction_en,
            'introduction_ar' => $this->introduction_ar,
            'introduction_en' => $this->introduction_en,
            'image' => MediaHelper::toPublicUrl($this->image)
                ?? url('/images/default-female-avatar.png'),
            'category' => new ArticleCategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('author')),
            'psychological_scale_id' => $this->psychological_scale_id,
            'psychological_scale' => new PsychologicalScaleResource($this->whenLoaded('psychologicalScale')),
            'category_id' => $this->category_id,
            'author_id' => $this->author_id,
            'attachments' => $this->attachments,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'is_published' => $this->is_published,
            'views' => $this->views,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
