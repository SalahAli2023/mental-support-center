<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'activity_id',
        'user_id',
        'status',
        'submission_date',
        'notes',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
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
            
            if (!$model->status) {
                $model->status = 'pending';
            }
        });
    }

    // العلاقات
    public function activity()
    {
        return $this->belongsTo(SessionActivity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ================= Accessors =================
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => ['ar' => 'معلق', 'en' => 'Pending'],
            'submitted' => ['ar' => 'تم التقديم', 'en' => 'Submitted'],
            'reviewed' => ['ar' => 'تم المراجعة', 'en' => 'Reviewed'],
            'approved' => ['ar' => 'معتمد', 'en' => 'Approved'],
            'rejected' => ['ar' => 'مرفوض', 'en' => 'Rejected'],
            'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
        ];
        
        return $statusLabels[$this->status] ?? ['ar' => 'غير معروف', 'en' => 'Unknown'];
    }

    public function getIsSubmittedAttribute()
    {
        return $this->status !== 'pending';
    }

    public function getIsCompletedAttribute()
    {
        return in_array($this->status, ['approved', 'completed']);
    }

    public function getHasNotesAttribute()
    {
        return !empty($this->notes);
    }

    // ================= Scopes =================
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['approved', 'completed']);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeWithNotes($query)
    {
        return $query->whereNotNull('notes')->where('notes', '!=', '');
    }
}