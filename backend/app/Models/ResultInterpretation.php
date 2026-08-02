<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ResultInterpretation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'scale_id',
        'min_score',
        'max_score',
        'interpretation_label_ar',
        'interpretation_label_en',
        'description_ar',
        'description_en',
        'color'
    ];

    public function scale()
    {
        return $this->belongsTo(PsychologicalScale::class);
    }

    public function scopeForScore($query, $score)
    {
        return $query->where('min_score', '<=', $score)
                    ->where('max_score', '>=', $score);
    }
}