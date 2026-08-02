<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'color',
    ];

    /**
     * Get the library items for this category.
     */
    public function libraryItems()
    {
        return $this->hasMany(LibraryItem::class, 'category_id');
    }
}
