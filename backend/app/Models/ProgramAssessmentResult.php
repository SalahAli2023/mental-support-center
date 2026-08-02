<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramAssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'program_assessment_id',
        'user_id',
        'user_assessment_id',
        'total_score',
        'interpretation_level',
        'assessment_data',
        'completed_at',
    ];

    protected $casts = [
        'assessment_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ================= العلاقات =================
    public function programAssessment()
    {
        return $this->belongsTo(ProgramAssessment::class, 'program_assessment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userAssessment()
    {
        return $this->belongsTo(UserAssessment::class, 'user_assessment_id');
    }

    // ================= Scopes =================
    public function scopeByProgramAssessment($query, $programAssessmentId)
    {
        return $query->where('program_assessment_id', $programAssessmentId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}





