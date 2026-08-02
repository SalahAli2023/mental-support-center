<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalResourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
    ];

    /**
     * Get the legal resources for this category.
     */
    public function legalResources()
    {
        return $this->hasMany(LegalResource::class, 'category_id');
    }
}
