<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'type',
        'description_ar',
        'description_en',
        'full_description_ar',
        'full_description_en',
        'media',
        'media_type',
        'date',
        'duration',
        'location_ar',
        'location_en',
        'topics_ar',
        'topics_en',
        'speakers',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'topics_ar' => 'array',
            'topics_en' => 'array',
            'speakers' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title_en ?? $event->title_ar);
            }
        });
    }
}
