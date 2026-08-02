<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ScaleQuestion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'scale_id',
        'question_text_ar',
        'question_text_en',
        'question_order'
    ];

    public function scale()
    {
        return $this->belongsTo(PsychologicalScale::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('option_order');
    }
}