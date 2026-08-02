<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Session extends Model
{
    use HasFactory;

    protected $table = 'video_sessions';

    protected $fillable = [
        'appointment_id',
        'room_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'progress_score',
        'therapist_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * Boot method to generate room_id automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->room_id)) {
                $session->room_id = self::generateRoomId();
            }
        });
    }

    /**
     * Generate a secure, unique room ID
     */
    public static function generateRoomId(): string
    {
        do {
            $roomId = Str::random(32);
        } while (self::where('room_id', $roomId)->exists());

        return $roomId;
    }

    /**
     * Get the appointment that owns the session.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(TherapistReview::class);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if session has ended
     */
    public function hasEnded(): bool
    {
        return in_array($this->status, ['ended', 'cancelled']);
    }
}

