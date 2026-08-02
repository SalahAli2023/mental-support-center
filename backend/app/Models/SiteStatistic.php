<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label_ar',
        'label_en',
        'value',
        'icon',
        'display_order',
    ];

    protected $casts = [
        'value' => 'integer',
        'display_order' => 'integer',
    ];
}




