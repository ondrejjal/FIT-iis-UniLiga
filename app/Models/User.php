<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false; // Your table doesn't have timestamps

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'first_name',
        'surname',
        'phone_number',
        'role',
        'profile_picture',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Override the password attribute accessor/mutator
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Accessor for profile picture URL
    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture ? Storage::url($this->profile_picture) : null;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'teams_users');
    }

    public function matches()
    {
        return Matches::query()
            ->where(function ($query) {
                $query->where('user1_id', $this->id)
                    ->orWhere('user2_id', $this->id);
            }); // can't be relationship due to orWhere which would mess up precedence 
    }

    public function teamMatches()
    {
        return Matches::query()
            ->where(function ($query) {
                $query->whereHas('team1.players', function ($q) {
                    $q->where('users.id', $this->id);
                })
                    ->orWhereHas('team1.captain', function ($q) {
                        $q->where('users.id', $this->id);
                    })
                    ->orWhereHas('team2.players', function ($q) {
                        $q->where('users.id', $this->id);
                    })
                    ->orWhereHas('team2.captain', function ($q) {
                        $q->where('users.id', $this->id);
                    });
            });
    }

    public function managedTeams()
    {
        return $this->hasMany(Team::class, 'user_id');
    }

    public function managedTournaments()
    {
        return $this->hasMany(Tournament::class, 'user_id');
    }

    public function approvedManagedTournaments()
    {
        return $this->managedTournaments()->where('pending', false);
    }

    public function pendingManagedTournaments()
    {
        return $this->managedTournaments()->where('pending', true);
    }

    public function teamTournaments()
    {
        return Tournament::query()
            ->where(function ($query) {
                $query->whereHas('teamContestants.players', function ($q) {
                    $q->where('users.id', $this->id);
                })
                    ->orWhereHas('teamContestants.captain', function ($q) {
                        $q->where('users.id', $this->id);
                    });
            });
    }

    public function approvedTeamTournaments()
    {
        return Tournament::query()
            ->where(function ($query) {
                $query->whereHas('approvedTeamContestants.players', function ($q) {
                    $q->where('users.id', $this->id);
                })
                    ->orWhereHas('approvedTeamContestants.captain', function ($q) {
                        $q->where('users.id', $this->id);
                    });
            });
    }
    public function pendingTeamTournaments()
    {
        return Tournament::query()
            ->where(function ($query) {
                $query->whereHas('pendingTeamContestants.players', function ($q) {
                    $q->where('users.id', $this->id);
                })
                    ->orWhereHas('pendingTeamContestants.captain', function ($q) {
                        $q->where('users.id', $this->id);
                    });
            });
    }
    public function singleTournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_contestants_single')
            ->withPivot('pending');
    }

    public function approvedSingleTournaments()
    {
        return $this->singleTournaments()->wherePivot('pending', false);
    }

    public function pendingSingleTournaments()
    {
        return $this->singleTournaments()->wherePivot('pending', true);
    }
}