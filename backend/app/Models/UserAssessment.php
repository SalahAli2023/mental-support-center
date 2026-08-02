<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAssessment extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'scale_id',
        'total_score',
        'interpretation_level',
        'assessment_data',
        'ip_address',
        'user_agent',
        'completed_at'
    ];

    protected $casts = [
        'assessment_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function psychologicalScale(): BelongsTo
    {
        return $this->belongsTo(PsychologicalScale::class, 'scale_id');
    }

    // Accessor للحصول على التفسير المفصل
    public function getInterpretationAttribute()
    {
        return $this->psychologicalScale->interpretations
            ->where('min_score', '<=', $this->total_score)
            ->where('max_score', '>=', $this->total_score)
            ->first();
    }
}
