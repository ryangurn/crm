<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'translations' => 'json',
    ];

    public function relTimezones()
    {
        return $this->morphToMany(Timezone::class, 'related', 'timezone_mapping');
    }
}
