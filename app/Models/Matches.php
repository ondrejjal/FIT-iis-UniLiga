<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    public $timestamps = false;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'user1_id',
        'user2_id',
        'team1_id',
        'team2_id',
        'round',
        'match_number',
        'next_match_id',
        'winner_id',
        'date',
        'starting_time',
        'result',
    ];

    protected $casts = [
        'date' => 'date',
        'starting_time' => 'datetime',
        'finishing_time' => 'datetime',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function nextMatch()
    {
        return $this->belongsTo(Matches::class, 'next_match_id');
    }

    public function previousMatches()
    {
        return $this->hasMany(Matches::class, 'next_match_id');
    }

    public function scopeFinished($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('winner_id');
            $q->orWhereNotNull('winner_team_id');
        });
    }

}