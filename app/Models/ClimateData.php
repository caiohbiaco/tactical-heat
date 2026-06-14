<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClimateData extends Model
{
    protected $fillable = [
        'search_id', 'month', 'temp_max_avg', 'temp_min_avg', 'humidity_avg', 'heat_index_avg', 'risk_level'
    ];

    public function search() { return $this->belongsTo(Search::class); }
}
