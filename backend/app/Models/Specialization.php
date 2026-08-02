<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
    ];

    /**
     * Get the therapists that have this specialization.
     */
    public function therapists()
    {
        return $this->belongsToMany(Therapist::class, 'therapist_specializations');
    }
}
