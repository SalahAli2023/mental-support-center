<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuestionOption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'question_id',
        'option_text_ar',
        'option_text_en',
        'score_value',
        'option_order'
    ];

    public function question()
    {
        return $this->belongsTo(ScaleQuestion::class);
    }
}