<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'author_ar',
        'author_en',
        'type',
        'category_id',
        'cover_image',
        'file_path',
        'file_size',
        'pages',
        'publish_date',
        'downloads',
        'views',
        'rating',
        'rating_count',
        'is_new',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'publish_date' => 'date',
            'downloads' => 'integer',
            'views' => 'integer',
            'pages' => 'integer',
            'rating' => 'decimal:2',
            'rating_count' => 'integer',
            'is_new' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the library item.
     */
    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }
}
