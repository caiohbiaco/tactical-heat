<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $fillable = [
        'user_id', 'city_name', 'latitude',
        'longitude', 'sport', 'year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function climateData()
    {
        return $this->hasMany(ClimateData::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport', 'name');
    }
}