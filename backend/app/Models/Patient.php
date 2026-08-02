<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'address_ar',
        'address_en',
        'therapy_goals_ar',
        'therapy_goals_en',
        'status',
        'medical_history_ar',
        'medical_history_en',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_info',
        'referral_source'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'name',
        'email',
        'phone',
        'avatar',
        'age',
        'upcoming_session',
        'last_session',
        'completed_sessions_count',
        'total_sessions_count'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع الحالات الصحية
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(PatientCondition::class);
    }

    /**
     * العلاقة مع الجلسات
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(PatientSession::class);
    }

    /**
     * الحصول على اسم المريض
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * الحصول على البريد الإلكتروني
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * الحصول على رقم الهاتف
     */
    public function getPhoneAttribute(): string
    {
        return $this->user->phone ?? '';
    }

    /**
     * الحصول على الصورة
     */
    public function getAvatarAttribute(): string
    {
        return $this->user->avatar ?? $this->getDefaultAvatar();
    }

    /**
     * الحصول على العمر
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * الحصول على العنوان بناءً على اللغة
     */
    public function getAddressAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->address_ar : $this->address_en;
    }

    /**
     * الحصول على أهداف العلاج بناءً على اللغة
     */
    public function getTherapyGoalsAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->therapy_goals_ar : $this->therapy_goals_en;
    }

    /**
     * الحصول على التاريخ الطبي بناءً على اللغة
     */
    public function getMedicalHistoryAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->medical_history_ar : $this->medical_history_en;
    }

    /**
     * الحصول على الجلسة القادمة
     */
    public function getUpcomingSessionAttribute()
    {
        return $this->sessions()
            ->where('status', 'scheduled')
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->orderBy('session_time')
            ->first();
    }

    /**
     * الحصول على آخر جلسة
     */
    public function getLastSessionAttribute()
    {
        return $this->sessions()
            ->where('status', 'completed')
            ->orderBy('session_date', 'desc')
            ->orderBy('session_time', 'desc')
            ->first();
    }

    /**
     * الحصول على عدد الجلسات المكتملة
     */
    public function getCompletedSessionsCountAttribute(): int
    {
        return $this->sessions()->where('status', 'completed')->count();
    }

    /**
     * الحصول على إجمالي عدد الجلسات
     */
    public function getTotalSessionsCountAttribute(): int
    {
        return $this->sessions()->count();
    }

    /**
     * الحصول على الحالات الصحية كمصفوفة
     */
    public function getConditionsArrayAttribute(): array
    {
        return $this->conditions->pluck('condition_' . app()->getLocale())->toArray();
    }

    /**
     * التحقق إذا كان المريض نشط
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * الحصول على الصورة الافتراضية بناءً على الجنس
     */
    private function getDefaultAvatar(): string
    {
        if ($this->gender === 'female') {
            return '/images/default-female-avatar.png';
        } else {
            return '/images/default-male-avatar.png';
        }
    }

    /**
     * نطاق للمرضى النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * نطاق للمرضى غير النشطين
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * نطاق للبحث في المرضى
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * نطاق للمرضى الذين لديهم حالات صحية محددة
     */
    public function scopeWithCondition($query, $condition)
    {
        return $query->whereHas('conditions', function($q) use ($condition) {
            $q->where('condition_ar', 'like', "%{$condition}%")
              ->orWhere('condition_en', 'like', "%{$condition}%");
        });
    }

    /**
     * الحصول على إحصائيات المريض
     */
    public function getStats(): array
    {
        return [
            'total_sessions' => $this->total_sessions_count,
            'completed_sessions' => $this->completed_sessions_count,
            'scheduled_sessions' => $this->sessions()->where('status', 'scheduled')->count(),
            'cancelled_sessions' => $this->sessions()->where('status', 'cancelled')->count(),
            'total_conditions' => $this->conditions()->count(),
            'active_conditions' => $this->conditions()->where('is_active', true)->count(),
            'average_session_progress' => round($this->sessions()->where('status', 'completed')->avg('progress') ?? 0),
        ];
    }

    /**
     * تحديث حالة المريض
     */
    public function updateStatus(string $status): bool
    {
        $allowedStatuses = ['active', 'inactive'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $this->update(['status' => $status]);
        return true;
    }

    /**
     * إضافة حالة صحية جديدة
     */
    public function addCondition(array $conditionData): PatientCondition
    {
        return $this->conditions()->create($conditionData);
    }

    /**
     * إضافة جلسة جديدة
     */
    public function addSession(array $sessionData): PatientSession
    {
        return $this->sessions()->create($sessionData);
    }

    /**
     * الحصول على الجلسات القادمة
     */
    public function upcomingSessions($limit = 5)
    {
        return $this->sessions()
            ->where('status', 'scheduled')
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->orderBy('session_time')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على الجلسات الحديثة
     */
    public function recentSessions($limit = 5)
    {
        return $this->sessions()
            ->orderBy('session_date', 'desc')
            ->orderBy('session_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * التحقق إذا كان للمريض جلسات نشطة
     */
    public function hasActiveSessions(): bool
    {
        return $this->sessions()
            ->where('status', 'scheduled')
            ->where('session_date', '>=', now())
            ->exists();
    }

    /**
     * الحصول على تقدم العلاج العام
     */
    public function getOverallProgress(): int
    {
        $completedSessions = $this->sessions()->where('status', 'completed')->get();
        
        if ($completedSessions->isEmpty()) {
            return 0;
        }

        return (int) $completedSessions->avg('progress');
    }

    /**
     * Format patient data for API responses
     */
    public function formatForApi(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dateOfBirth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'address' => $this->address,
            'therapyGoals' => $this->therapy_goals,
            'status' => $this->status,
            'conditions' => $this->conditions_array,
            'totalConditions' => $this->conditions()->count(),
            'totalSessions' => $this->total_sessions_count,
            'completedSessions' => $this->completed_sessions_count,
            'lastSession' => $this->last_session?->session_date?->format('Y-m-d'),
            'nextSession' => $this->upcoming_session?->session_date?->format('Y-m-d'),
            'createdAt' => $this->created_at->format('Y-m-d'),
            'overallProgress' => $this->getOverallProgress(),
            'hasActiveSessions' => $this->hasActiveSessions(),
            'emergencyContact' => [
                'name' => $this->emergency_contact_name,
                'phone' => $this->emergency_contact_phone
            ],
            'insuranceInfo' => $this->insurance_info,
            'referralSource' => $this->referral_source
        ];
    }
}