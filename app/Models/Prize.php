<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tournament_id',
        'prize_index',
        'description',
    ];

    protected $casts = [
        'prize_index' => 'integer',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
