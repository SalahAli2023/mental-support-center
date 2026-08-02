<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'program_id',
        'scale_id',
        'assessment_type',
        'is_mandatory',
        'order',
        'instructions_ar',
        'instructions_en',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
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
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function scale()
    {
        return $this->belongsTo(PsychologicalScale::class, 'scale_id');
    }

    public function results()
    {
        return $this->hasMany(ProgramAssessmentResult::class, 'program_assessment_id');
    }

    // ================= Accessors =================
    public function getInstructionsAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->instructions_ar : $this->instructions_en;
    }

    // ================= Scopes =================
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    public function scopePreAssessment($query)
    {
        return $query->where('assessment_type', 'pre');
    }

    public function scopePostAssessment($query)
    {
        return $query->where('assessment_type', 'post');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}





