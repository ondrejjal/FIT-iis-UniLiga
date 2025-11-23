<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tournament extends Model
{
    public $timestamps = false; // Your table doesn't have timestamps

    protected $fillable = [
        'user_id',
        'pending',
        'type',
        'max_participants',
        'min_participants',
        'name',
        'date',
        'end_date',
        'starting_time',
        'description',
    ];

    protected $casts = [
        'pending' => 'boolean',
        'date' => 'date',
        'end_date' => 'date',
        'starting_time' => 'datetime',
    ];

    // Accessor for short description
    public function getShortDescriptionAttribute()
    {
        return $this->description ? Str::limit($this->description, 50) : null;
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function singleContestants()
    {
        return $this->belongsToMany(User::class, 'tournament_contestants_single', 'tournament_id', 'user_id')
            ->withPivot('pending', 'order')
            ->orderBy('order');
    }

    public function approvedSingleContestants(
    ) {
        return $this->singleContestants()->wherePivot('pending', false);
    }

    public function pendingSingleContestants()
    {
        return $this->singleContestants()->wherePivot('pending', true);
    }

    public function teamContestants()
    {
        return $this->belongsToMany(Team::class, 'tournament_contestants_teams', 'tournament_id', 'team_id')
            ->withPivot('pending', 'order')
            ->orderBy('order');
    }

    public function approvedTeamContestants()
    {
        return $this->teamContestants()->wherePivot('pending', false);
    }

    public function pendingTeamContestants()
    {
        return $this->teamContestants()->wherePivot('pending', true);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function matches()
    {
        return $this->hasMany(Matches::class, 'tournament_id');
    }

    public function hasBracket()
    {
        return $this->matches()->exists();
    }

    public function scopeApproved($query)
    {
        return $query->where('pending', false);
    }

}