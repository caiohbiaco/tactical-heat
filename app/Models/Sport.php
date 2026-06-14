<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'name', 'temp_medium_risk', 'temp_high_risk', 'humidity_medium_risk', 'federation_protocol'
    ];

    public function searches()
    {
        return $this->hasMany(Search::class, 'sport', 'name');
    }
}
