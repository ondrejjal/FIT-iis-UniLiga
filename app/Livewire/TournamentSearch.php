<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Locked;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\User;
use Log;

class TournamentSearch extends Component
{
    use WithPagination;

    /* Filters */
    public $query = '';
    public $filterType = ''; // 'team', 'individual', or '' for all
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'date_asc'; // 'date_asc', 'date_desc', 'name_asc', 'name_desc'
    public $relationship = 'all'; // 'all', 'organizer', 'participant'

    /* Component Style Options */
    public $showSearch = true;
    public $showFilters = true;
    public $filtersOpen = false;
    public $perPage = 10;

    /* Context */
    #[Locked]
    public $user_id;

    #[Locked]
    public $team_id;

    #[Locked]
    public $mode = 'global'; // 'global', 'user', 'user-pending-manage', 'user-pending-join' 'team' 'team-pending-join'

    /**
     * Checks and sets the context for the tournament search. 
     * @param mixed $user_id
     * @param mixed $team_id
     * @param mixed $mode
     * @return void
     */
    public function mount($user_id = null, $team_id = null, $mode = 'global')
    {
        $this->user_id = $user_id;
        $this->team_id = $team_id;
        $this->mode = $mode;

        // Authorization for user pending modes
        if (in_array($mode, ['user-pending-manage', 'user-pending-join'])) {
            if (!$this->user_id) {
                abort(403, 'Unauthorized access to private tournament listings.');
            }
            $user = User::findOrFail($this->user_id);
            $this->authorize('viewPending', $user);
        }

        // Authorization for team pending mode
        if ($mode === 'team-pending-join') {
            if (!$this->team_id) {
                abort(403, 'Unauthorized access to private tournament listings.');
            }
            $team = Team::findOrFail($this->team_id);
            $this->authorize('viewPending', $team);
        }
    }

    /* Updating handlers to reset pagination */
    public function updatingQuery()
    {
        $this->resetPage();
    }
    public function updatingFilterType()
    {
        $this->resetPage();
    }
    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }
    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }
    public function updatingSortBy()
    {
        $this->resetPage();
    }
    public function updatingRelationship()
    {
        $this->resetPage();
    }
    public function clearFilters()
    {
        $this->query = '';
        $this->filterType = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->sortBy = 'date_asc';
        $this->relationship = 'all';
        $this->resetPage();
    }

    /**
     * Renders the tournament search component with applied filters and sorting.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $tournaments_query = $this->getBaseQuery();
        // Search filter
        if ($this->query) {
            $tournaments_query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->query}%")
                    ->orWhere('description', 'LIKE', "%{$this->query}%");
            });
        }

        // Type filter
        if ($this->filterType) {
            $tournaments_query->where('type', $this->filterType);
        }

        // Date range filter
        if ($this->filterDateFrom) {
            $tournaments_query->where('date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $tournaments_query->where('date', '<=', $this->filterDateTo);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'date_asc':
                $tournaments_query->orderBy('date', 'asc');
                break;
            case 'date_desc':
                $tournaments_query->orderBy('date', 'desc');
                break;
            case 'name_asc':
                $tournaments_query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $tournaments_query->orderBy('name', 'desc');
                break;
        }

        $tournaments_list = $tournaments_query->paginate($this->perPage);

        return view('livewire.tournament-search', [
            'tournaments' => $tournaments_list,
            'showSearch' => $this->showSearch,
            'showFilters' => $this->showFilters,
            'mode' => $this->mode
        ]);
    }

    /* Gets the base query depending on the mode and context */
    private function getBaseQuery()
    {
        Log::info('Getting base query for mode: ' . $this->mode);
        $tournaments_query = Tournament::query();
        switch ($this->mode) {
            case 'user-pending-manage':
                if ($this->user_id) {
                    $user = User::findOrFail($this->user_id);
                    $tournaments_query = $user->managedTournaments()->where('tournaments.pending', true);
                }
                break;
            case 'user-pending-join':
                if ($this->user_id) {
                    $user = User::findOrFail($this->user_id);
                    $tournaments_query = Tournament::query()->where(function ($query) use ($user) {
                        $query->whereHas('pendingSingleContestants', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        })
                            ->orWhereHas('pendingTeamContestants.players', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            })
                            ->orWhereHas('pendingTeamContestants.captain', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            });
                    })->where('tournaments.pending', false);
                }
                break;
            case 'user':
                $tournaments_query = $this->userQuery();
                break;
            case 'team':
                if ($this->team_id) {
                    $team = Team::findOrFail($this->team_id);
                    $tournaments_query = $team->tournaments()->where('tournaments.pending', false);

                }
                break;
            case 'team-pending-join':
                if ($this->team_id) {
                    $team = Team::findOrFail($this->team_id);
                    $tournaments_query = $team->tournaments()->wherePivot('pending', true);
                }
                break;
            default:
                $tournaments_query = Tournament::query()->where('pending', false);
                break;
        }
        return $tournaments_query;
    }

    /* Helper to get tournaments related to a user based on relationship filter */
    private function userQuery()
    {
        if (!$this->user_id) {
            return Tournament::where('id', 0)->get(); // empty query
        }
        $user = User::findOrFail($this->user_id);
        switch ($this->relationship) {
            case 'organizer':
                $tournaments_query = $user->managedTournaments()->where('tournaments.pending', false);
                break;
            case 'participant':
                $tournaments_query = Tournament::query()->where(function ($query) use ($user) {
                    $query->whereHas('approvedSingleContestants', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                        ->orWhereHas('approvedTeamContestants.players', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        })
                        ->orWhereHas('approvedTeamContestants.captain', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })->where('tournaments.pending', false);
                break;
            default:
                $tournaments_query = Tournament::query()->where(function ($query) use ($user) {
                    $query->whereHas('approvedSingleContestants', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                        ->orWhereHas('approvedTeamContestants.players', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        })
                        ->orWhereHas('approvedTeamContestants.captain', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        })
                        ->orWhereHas('organizer', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })->where('tournaments.pending', false);
                break;
        }
        return $tournaments_query;
    }
}