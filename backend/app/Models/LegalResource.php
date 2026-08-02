<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'law_type',
        'article_number_ar',
        'article_number_en',
        'text_ar',
        'text_en',
        'category_id',
    ];

    /**
     * Get the category that owns the legal resource.
     */
    public function category()
    {
        return $this->belongsTo(LegalResourceCategory::class, 'category_id');
    }
}
