<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'name_ar',
        'name_en',
        'institution_ar',
        'institution_en',
        'year'
    ];

    /**
     * العلاقة مع المعالج
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}