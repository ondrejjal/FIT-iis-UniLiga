<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use \App\Models\Team;
use \App\Models\User;
use Illuminate\Support\Facades\Validator;
use Log;

class UserSearch extends Component
{
    use WithPagination;
    public $teamId = null;
    public $query = '';
    public $perPage = 5;
    public $showSearch = true;
    public $mode = 'display'; // 'display', 'add', 'remove'

    public $userRedirect = null;

    protected $listeners = ['team-players-updated' => '$refresh'];

    // Listen for player-added event to refresh the list

    public function mount($teamId = null, $showSearch = true, $perPage = 5, $mode = 'display', $userRedirect = null)
    {
        $this->teamId = $teamId;
        $this->showSearch = $showSearch;
        $this->perPage = $perPage;
        $this->mode = $mode;
        $this->userRedirect = $userRedirect;
    }

    public function render()
    {
        // Reload team to get fresh player data
        $team = $this->teamId ? Team::find($this->teamId) : null;

        $user_list = [];
        $capitan = null;

        if ($this->mode === 'display') {
            if ($team) {
                $user_list = $team->players()
                    ->where(function ($query) {
                        $query->where('username', 'LIKE', "%{$this->query}%")
                            ->orWhere('first_name', 'LIKE', "%{$this->query}%")
                            ->orWhere('surname', 'LIKE', "%{$this->query}%")
                            ->orWhere('email', 'LIKE', "%{$this->query}%");
                    })
                    ->paginate($this->perPage);
                $capitan = $team->captain;
            } else {
                $user_list = User::where(function ($q) {
                    $q->where('username', 'LIKE', "%{$this->query}%")
                        ->orWhere('first_name', 'LIKE', "%{$this->query}%")
                        ->orWhere('surname', 'LIKE', "%{$this->query}%")
                        ->orWhere('email', 'LIKE', "%{$this->query}%");
                })
                    ->paginate($this->perPage);
            }
        }

        if ($this->mode === 'add') {
            // Reload team players to get fresh data
            $prohibited_list = $team ? $team->players()->pluck('users.id')->toArray() : [];
            if ($team && $team->user_id) {
                $prohibited_list[] = $team->user_id;
            }

            Log::info('Add mode - Prohibited IDs:', [
                'prohibited_list' => $prohibited_list,
                'team_id' => $this->teamId,
                'captain_id' => $team ? $team->user_id : null,
                'query' => $this->query
            ]);

            $user_list = User::where(function ($q) {
                $q->where('username', 'LIKE', "%{$this->query}%")
                    ->orWhere('first_name', 'LIKE', "%{$this->query}%")
                    ->orWhere('surname', 'LIKE', "%{$this->query}%")
                    ->orWhere('email', 'LIKE', "%{$this->query}%");
            })
                ->whereNotIn('id', $prohibited_list)
                ->paginate($this->perPage);

            Log::info('Add mode - Results:', [
                'result_count' => $user_list->count(),
                'result_ids' => $user_list->pluck('id')->toArray()
            ]);
        }

        if ($this->mode === 'remove') {
            $user_list = $team->players()
                ->where(function ($query) {
                    $query->where('username', 'LIKE', "%{$this->query}%")
                        ->orWhere('first_name', 'LIKE', "%{$this->query}%")
                        ->orWhere('surname', 'LIKE', "%{$this->query}%")
                        ->orWhere('email', 'LIKE', "%{$this->query}%");
                })
                ->paginate($this->perPage);

            $capitan = $team->captain;
        }

        return view('livewire.user-search', [
            'users' => $user_list,
            'captain' => $capitan,
            'mode' => $this->mode
        ]);
    }

    public function addUser($userId)
    {
        $validated = Validator::make(
            [
                'teamId' => $this->teamId,
                'userId' => $userId
            ],
            [
                'teamId' => 'required|exists:teams,id',
                'userId' => 'required|exists:users,id'
            ]
        )->validate();

        $team = Team::findOrFail($validated['teamId']);
        $this->authorize('update', $team);

        // Check if this team is registered for any tournaments
        $teamTournaments = $team->tournaments()
            ->wherePivot('pending', false) // Only check approved registrations
            ->where('type', 'team')
            ->get();

        if ($teamTournaments->count() > 0) {
            $user = User::findOrFail($userId);

            // Check if the user is in any other team registered for the same tournaments
            foreach ($teamTournaments as $tournament) {
                $otherRegisteredTeams = $tournament->teamContestants()
                    ->wherePivot('pending', false)
                    ->where('teams.id', '!=', $team->id)
                    ->with('players')
                    ->get();

                foreach ($otherRegisteredTeams as $otherTeam) {
                    $otherTeamPlayerIds = $otherTeam->players()->pluck('users.id')->toArray();
                    $otherTeamPlayerIds[] = $otherTeam->user_id; // Add captain

                    if (in_array($userId, $otherTeamPlayerIds)) {
                        session()->flash('error', "Cannot add {$user->username}. They are already a member of team '{$otherTeam->name}' which is registered for tournament '{$tournament->name}'.");
                        return;
                    }
                }
            }
        }

        $team->players()->attach($validated['userId']);
        $this->dispatch('team-players-updated');

        Log::info('User attached', ['userId' => $userId, 'teamId' => $this->teamId]);
    }

    public function removeUser($userId)
    {
        // this has to be there because userId does not come from the component nor the request
        $validated = Validator::make(
            [
                'teamId' => $this->teamId,
                'userId' => $userId
            ],
            [
                'teamId' => 'required|exists:teams,id',
                'userId' => 'required|exists:users,id'
            ]
        )->validate();

        $team = Team::findOrFail($validated['teamId']);
        $this->authorize('update', $team);

        $team->players()->detach($validated['userId']);
        $this->dispatch('team-players-updated');

        Log::info('User removed', ['userId' => $userId, 'teamId' => $this->teamId]);
    }
}