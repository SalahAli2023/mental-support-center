<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserMessage extends Model
{
    use HasFactory;

    public const TYPES = ['inquiry', 'complaint', 'review'];
    public const STATUSES = ['new', 'in_progress', 'resolved'];

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message_type',
        'message',
        'response',
        'status',
        'is_read',
        'read_at',
        'is_public',
        'public_at',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_public' => 'boolean',
        'public_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}

