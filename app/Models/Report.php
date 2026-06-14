<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['search_id', 'content', 'generated_at'];

    protected $casts = ['generated_at' => 'datetime'];

    public function search() { return $this->belongsTo(Search::class); }
}
