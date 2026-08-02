<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'condition_ar',
        'condition_en',
        'diagnosis_date',
        'severity',
        'treatment_plan_ar',
        'treatment_plan_en',
        'medications_ar',
        'medications_en',
        'is_active',
        'notes_ar',
        'notes_en'
    ];

    protected $casts = [
        'diagnosis_date' => 'date',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // النطاقات (Scopes)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithSeverity($query, $severity)
    {
        if ($severity) {
            return $query->where('severity', $severity);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('condition_ar', 'like', "%{$search}%")
                  ->orWhere('condition_en', 'like', "%{$search}%")
                  ->orWhere('notes_ar', 'like', "%{$search}%")
                  ->orWhere('notes_en', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    // السمات (Attributes)
    public function getSeverityTextAttribute()
    {
        $severityMap = [
            'mild' => 'خفيفة',
            'moderate' => 'متوسطة',
            'severe' => 'شديدة'
        ];

        return $severityMap[$this->severity] ?? $this->severity;
    }
}