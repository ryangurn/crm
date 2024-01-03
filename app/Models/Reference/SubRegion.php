<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubRegion extends Model
{
    protected $fillable = [
        'name',
        'region_id'
    ];

    protected $casts = [
        'translations' => 'json'
    ];
}
