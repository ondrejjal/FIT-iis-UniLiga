<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Log;
use App\Models\Team;

class MatchSearch extends Component
{
    use WithPagination;

    public $user_id;

    public $team_id;

    public $mode = 'both'; // individual, team

    public $perPage = 5;

    public function mount($user_id = null, $team_id = null, $mode = 'both', $perPage = 5)
    {
        $this->user_id = $user_id;
        $this->team_id = $team_id;
        $this->mode = $mode;
        $this->perPage = $perPage;
    }

    public function render()
    {
        /* User Matches */
        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            if ($this->mode == 'individual') {
                $user_matches = $user->matches()->orderBy('date', 'desc')->paginate($this->perPage);
                Log::info('User matches', ['matches' => $user_matches]);
                return view('livewire.match-search', [
                    'user_matches' => $user_matches,
                ]);
            }
            if ($this->mode == 'team') {
                $user_team_matches = $user->teamMatches()->orderBy('date', 'desc')->paginate($this->perPage);
                return view('livewire.match-search', [
                    'user_team_matches' => $user_team_matches
                ]);
            }
            if ($this->mode == 'both') {
                $user_matches = $user->matches()->orderBy('date', 'desc')->paginate($this->perPage);
                $user_team_matches = $user->teamMatches()->orderBy('date', 'desc')->paginate($this->perPage);
                return view('livewire.match-search', [
                    'user_matches' => $user_matches,
                    'user_team_matches' => $user_team_matches
                ]);
            }
        }

        /* Team Matches */
        if ($this->team_id) {
            $team = Team::findOrFail($this->team_id);
            $team_matches = $team->matches()->orderBy('date', 'desc')->paginate($this->perPage);
            return view('livewire.match-search', [
                'team_matches' => $team_matches
            ]);
        }

        return view('livewire.match-search');

    }
}