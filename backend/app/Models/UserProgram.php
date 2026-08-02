<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'program_id',
        'enrollment_date',
        'progress_percentage',
        'status',
        'current_phase_id',
        'current_session_id',
        'current_activity_id',
        'started_at',
        'completed_at',
        'progress_data',
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'progress_percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_data' => 'array',
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
                $model->id = (string) Str::uuid();
            }
            
            if (!$model->enrollment_date) {
                $model->enrollment_date = now();
            }
            
            if (!$model->status) {
                $model->status = 'enrolled';
            }
        });
    }

    // ================= العلاقات =================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function sessionCompletions()
    {
        return $this->hasMany(SessionCompletion::class, 'user_id', 'user_id')
                    ->whereHas('session', function($query) {
                        $query->where('program_id', $this->program_id);
                    });
    }

    public function currentPhase()
    {
        return $this->belongsTo(ProgramPhase::class, 'current_phase_id');
    }

    public function currentSession()
    {
        return $this->belongsTo(ProgramSession::class, 'current_session_id');
    }

    public function currentActivity()
    {
        return $this->belongsTo(SessionActivity::class, 'current_activity_id');
    }

    // ================= Accessors =================
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'enrolled'  => ['ar' => 'مسجل', 'en' => 'Enrolled'],
            'in_progress' => ['ar' => 'جاري', 'en' => 'In Progress'],
            'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
            'dropped'   => ['ar' => 'متوقف', 'en' => 'Dropped'],
            'pending'   => ['ar' => 'معلق', 'en' => 'Pending'],
        ];
        
        return $statusLabels[$this->status] ?? ['ar' => 'غير معروف', 'en' => 'Unknown'];
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed' || $this->progress_percentage >= 100;
    }

    public function getIsInProgressAttribute()
    {
        return $this->status === 'in_progress' && $this->progress_percentage < 100;
    }

    // ================= Scopes =================
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'in_progress']);
    }
}