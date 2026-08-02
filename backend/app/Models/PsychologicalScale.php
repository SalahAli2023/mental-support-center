<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PsychologicalScale extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image_url',
        'max_score',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(ScaleQuestion::class, 'scale_id');
    }

    public function interpretations()
    {
        return $this->hasMany(ResultInterpretation::class, 'scale_id');
    }

    public function activeQuestions()
    {
        return $this->hasMany(ScaleQuestion::class, 'scale_id')->orderBy('question_order');
    }

    /**
     * Retrieve the model for route model binding.
     * Supports UUIDs since we're using HasUuids trait.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }
}