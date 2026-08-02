<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'program_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'phase_order',
        'is_active',
        'is_hidden',
    ];

    protected $casts = [
        'phase_order' => 'integer',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
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

    public function sessions()
    {
        return $this->hasMany(ProgramSession::class, 'phase_id')
                    ->orderBy('session_order');
    }

    public function activeSessions()
    {
        return $this->sessions()->where('is_active', true);
    }

    // ================= Accessors =================
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getSessionsCountAttribute()
    {
        return $this->sessions()->count();
    }

    // ================= Scopes =================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('phase_order');
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }
}





