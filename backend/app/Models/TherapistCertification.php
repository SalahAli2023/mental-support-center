<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'name',
        'issuing_authority',
        'year_obtained'
    ];

    protected $casts = [
        'year_obtained' => 'integer',
    ];

    /**
     * العلاقة مع المعالج
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}