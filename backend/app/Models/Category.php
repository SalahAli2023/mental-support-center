<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function psychologicalScales()
    {
        return $this->hasMany(PsychologicalScale::class);
    }

    public function activeScales()
    {
        return $this->hasMany(PsychologicalScale::class)->where('is_active', true);
    }
}