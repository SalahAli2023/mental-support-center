<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'color',
        'description_ar',
        'description_en',
    ];

    /**
     * Get the articles for this category.
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }
}
