<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionHomework extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'session_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'instructions_ar',
        'instructions_en',
        'is_mandatory',
        'completion_type',
        'completion_config',
        'homework_order',
        'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'completion_config' => 'array',
        'homework_order' => 'integer',
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
    public function session()
    {
        return $this->belongsTo(ProgramSession::class, 'session_id');
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class, 'homework_id');
    }

    public function completedSubmissions()
    {
        return $this->submissions()->whereIn('status', ['completed', 'approved']);
    }

    public function pendingSubmissions()
    {
        return $this->submissions()->where('status', 'pending');
    }

    // ================= Accessors =================
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getInstructionsAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->instructions_ar : $this->instructions_en;
    }

    // ================= Scopes =================
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('homework_order');
    }
}





