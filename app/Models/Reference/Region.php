<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'name'  
    ];
    
    protected $casts = [
        'translations' => 'json'  
    ];
}
