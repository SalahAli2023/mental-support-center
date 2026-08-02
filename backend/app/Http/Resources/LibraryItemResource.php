<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\MediaHelper;

class LibraryItemResource extends JsonResource
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
            'description' => $locale === 'ar' ? $this->description_ar : $this->description_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'author' => $locale === 'ar' ? $this->author_ar : $this->author_en,
            'author_ar' => $this->author_ar,
            'author_en' => $this->author_en,
            'type' => $this->type,
            'category' => new LibraryCategoryResource($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'cover_image' => MediaHelper::toPublicUrl($this->cover_image),
            'file_path' => MediaHelper::toPublicUrl($this->file_path),
            'file_size' => $this->file_size,
            'pages' => $this->pages,
            'publish_date' => $this->publish_date?->format('Y-m-d'),
            'downloads' => $this->downloads,
            'views' => $this->views,
            'rating' => (float) $this->rating,
            'rating_count' => $this->rating_count,
            'is_new' => $this->is_new,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
