<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'session_id',
        'user_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // العلاقات
    public function session()
    {
        return $this->belongsTo(ProgramSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ================= Accessors =================
    public function getCompletionTimeAttribute()
    {
        if ($this->completed_at && $this->created_at) {
            return $this->created_at->diffInHours($this->completed_at);
        }
        return null;
    }

    public function getCompletionStatusAttribute()
    {
        return $this->is_completed ? 'مكتمل' : 'غير مكتمل';
    }

    // ================= Scopes =================
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}