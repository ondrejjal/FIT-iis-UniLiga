<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Team extends Model
{

    public $timestamps = false; // Your table doesn't have timestamps

    protected $fillable = [
        'user_id',
        'name',
        'logo'
    ];

    // Accessor for logo URL
    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    // Captain / owner of the team
    public function captain()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Team members (players)
    public function players()
    {
        return $this->belongsToMany(User::class, 'teams_users');
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_contestants_teams')
            ->withPivot('pending');
    }

    public function matches()
    {
        return Matches::query()
            ->where(function ($query) {
                $query->where('team1_id', $this->id)
                    ->orWhere('team2_id', $this->id);
            }); // can't be relationship due to orWhere which would mess up precedence
    }


}