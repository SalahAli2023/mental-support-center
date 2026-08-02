<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HomeworkSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'homework_id',
        'user_id',
        'status',
        'submission_text',
        'submission_data',
        'file_url',
        'submitted_at',
        'completed_at',
        'admin_notes',
    ];

    protected $casts = [
        'submission_data' => 'array',
        'submitted_at' => 'datetime',
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
    public function homework()
    {
        return $this->belongsTo(SessionHomework::class, 'homework_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ================= Scopes =================
    public function scopeByHomework($query, $homeworkId)
    {
        return $query->where('homework_id', $homeworkId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'approved']);
    }
}





