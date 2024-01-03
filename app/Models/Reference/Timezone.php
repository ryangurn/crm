<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $fillable = [
        'zone_name',
    ];

    protected $casts = [
        'gmt_offset' => 'integer',
        'gmt_offset_name' => 'string',
    ];
}
