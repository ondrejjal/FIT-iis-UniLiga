<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;
use Log;

class TournamentController extends Controller
{
    public function index()
    {
        // Show only approved tournaments to public
        $tournaments = Tournament::where('pending', false)
            ->orderBy('date', 'desc')
            ->get();
        return view('tournaments.index', ['tournaments' => $tournaments]);
    }

    public function show($id)
    {
        $tournament = Tournament::findOrFail($id);
        $user = auth()->user();

        $user_match = null;
        if ($tournament->type == 'team') {
            $user_match = $user ? $user->teamMatches()->where('tournament_id', $tournament->id)->orderBy('date', 'desc')->first() : null;
        }
        if ($tournament->type == 'individual') {
            $user_match = $user ? $user->matches()->where('tournament_id', $tournament->id)->orderBy('date', 'desc')->first() : null;
        }


        // Prepare data for individual tournaments
        $isRegistered = false;
        $isFull = false;
        $userTeams = collect();
        $registeredTeamIds = [];
        $availableTeams = collect();
        $userRegisteredTeams = collect();

        if (auth()->check()) {
            if ($tournament->type === 'individual') {
                // Individual tournament data
                $isRegistered = $tournament->singleContestants()
                    ->where('user_id', auth()->id())
                    ->exists();
                $isFull = $tournament->singleContestants()->count() >= $tournament->max_participants;
            } else {
                // Team tournament data
                $userTeams = Team::where('user_id', auth()->id())->get();
                $registeredTeamIds = $tournament->teamContestants()->pluck('teams.id')->toArray();
                $availableTeams = $userTeams->whereNotIn('id', $registeredTeamIds);
                $userRegisteredTeams = $userTeams->whereIn('id', $registeredTeamIds);
                $isFull = $tournament->teamContestants()->count() >= $tournament->max_participants;
            }
        }

        // Get approved contestants for display
        $approvedTeams = collect();
        $approvedPlayers = collect();

        if ($tournament->type === 'team') {
            $approvedTeams = $tournament->teamContestants()
                ->wherePivot('pending', false)
                ->get();
        } else {
            $approvedPlayers = $tournament->singleContestants()
                ->wherePivot('pending', false)
                ->get();
        }

        // Get bracket data if bracket exists
        $matches = null;
        $rounds = null;
        $maxRound = null;
        if ($tournament->hasBracket()) {
            $matches = $tournament->matches()
                ->orderBy('round')
                ->orderBy('id')
                ->get();
            $rounds = $matches->groupBy('round');
            $maxRound = $rounds->keys()->max();
        }

        return view('tournaments.show', compact(
            'tournament',
            'isRegistered',
            'isFull',
            'userTeams',
            'registeredTeamIds',
            'availableTeams',
            'userRegisteredTeams',
            'approvedTeams',
            'approvedPlayers',
            'matches',
            'rounds',
            'maxRound',
            'user_match'
        ));
    }

    public function create()
    {
        $today = now()->format('Y-m-d');
        return view('tournaments.create', compact('today'));
    }

    public function edit($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('update', $tournament);
        return view('tournaments.edit', ['tournament' => $tournament]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'starting_time' => 'required',
            'end_date' => 'nullable|date|after_or_equal:date',
            'type' => 'required|in:individual,team',
            'max_participants' => 'required|integer|min:2|max:256',
            'min_participants' => 'required|integer|min:2|lte:max_participants',
        ]);

        $tournament = new Tournament();
        $tournament->user_id = auth()->id();
        $tournament->pending = true;
        $tournament->name = $validated['name'];
        $tournament->description = $validated['description'];
        $tournament->date = $validated['date'];
        $tournament->end_date = $validated['end_date'] ?? null;
        $tournament->starting_time = $validated['starting_time'];
        $tournament->type = $validated['type'];
        $tournament->max_participants = $validated['max_participants'];
        $tournament->min_participants = $validated['min_participants'];
        $tournament->save();

        return redirect()->route('tournaments.show', ['id' => $tournament->id])
            ->with('success', 'Tournament created successfully and is pending admin approval!');
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'starting_time' => 'required',
            'end_date' => 'nullable|date|after_or_equal:date',
            'type' => 'required|in:individual,team',
            'max_participants' => 'required|integer|min:2',
            'min_participants' => 'required|integer|min:2|lte:max_participants',
        ]);

        $tournament->name = $validated['name'];
        $tournament->description = $validated['description'];
        $tournament->date = $validated['date'];
        $tournament->end_date = $validated['end_date'] ?? null;
        $tournament->starting_time = $validated['starting_time'];
        $tournament->type = $validated['type'];
        $tournament->max_participants = $validated['max_participants'];
        $tournament->min_participants = $validated['min_participants'];
        $tournament->save();

        return redirect()->route('tournaments.show', ['id' => $tournament->id])
            ->with('success', 'Tournament updated successfully!');
    }

    public function destroy($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('delete', $tournament);
        $tournament->delete();

        return redirect()->route('tournaments.index')
            ->with('success', 'Tournament deleted successfully!');
    }

    // Registration methods
    public function register($id)
    {
        $tournament = Tournament::findOrFail($id);
        $user = auth()->user();

        // Check if tournament is individual type
        if ($tournament->type !== 'individual') {
            return redirect()->back()->with('error', 'This is a team tournament. Register with your team instead.');
        }

        // Check if already registered
        if ($tournament->singleContestants()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You are already registered for this tournament.');
        }

        // Check if tournament is full
        $currentCount = $tournament->singleContestants()->count();
        if ($currentCount >= $tournament->max_participants) {
            return redirect()->back()->with('error', 'This tournament is full.');
        }

        // Register with pending status
        $tournament->singleContestants()->attach($user->id, ['pending' => true]);

        return redirect()->back()->with('success', 'Registration submitted! Waiting for organizer approval.');
    }

    public function registerTeam(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        $team = Team::findOrFail($validated['team_id']);

        // Check if user is the team captain
        if ($team->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Only the team captain can register the team.');
        }

        // Check if tournament is team type
        if ($tournament->type !== 'team') {
            return redirect()->back()->with('error', 'This is an individual tournament. Register as an individual instead.');
        }

        // Check if team already registered
        if ($tournament->teamContestants()->where('team_id', $team->id)->exists()) {
            return redirect()->back()->with('error', 'This team is already registered for this tournament.');
        }

        // Check if tournament is full
        $currentCount = $tournament->teamContestants()->count();
        if ($currentCount >= $tournament->max_participants) {
            return redirect()->back()->with('error', 'This tournament is full.');
        }

        // NEW CHECK: Prevent players from being in multiple teams in the same tournament
        // Get all players from the team being registered (captain + members)
        $teamPlayerIds = $team->players()->pluck('users.id')->toArray();
        $teamPlayerIds[] = $team->user_id; // Add captain
        $teamPlayerIds = array_unique($teamPlayerIds); // Remove duplicates if captain is also a member

        // Get all teams already registered in this tournament (both pending and approved)
        $registeredTeams = $tournament->teamContestants()->with('players')->get();

        // Check if any player from the new team is in any already-registered team
        foreach ($registeredTeams as $registeredTeam) {
            $registeredPlayerIds = $registeredTeam->players()->pluck('users.id')->toArray();
            $registeredPlayerIds[] = $registeredTeam->user_id; // Add captain
            $registeredPlayerIds = array_unique($registeredPlayerIds);

            // Find common players
            $commonPlayers = array_intersect($teamPlayerIds, $registeredPlayerIds);

            if (!empty($commonPlayers)) {
                // Get the names of conflicting players
                $conflictingPlayers = \App\Models\User::whereIn('id', $commonPlayers)->pluck('username')->toArray();
                $playerNames = implode(', ', $conflictingPlayers);

                return redirect()->back()->with('error',
                    "Cannot register this team. The following player(s) are already registered with team '{$registeredTeam->name}': {$playerNames}");
            }
        }

        // Register with pending status
        $tournament->teamContestants()->attach($team->id, ['pending' => true]);

        return redirect()->back()->with('success', 'Team registration submitted! Waiting for organizer approval.');
    }

    public function unregister($id)
    {
        $tournament = Tournament::findOrFail($id);
        $user = auth()->user();

        if ($tournament->type === 'individual') {
            $tournament->singleContestants()->detach($user->id);
        }

        return redirect()->back()->with('success', 'You have been unregistered from this tournament.');
    }

    public function unregisterTeam($id, $teamId)
    {
        $tournament = Tournament::findOrFail($id);
        $team = Team::findOrFail($teamId);

        // Check if user is the team captain
        if ($team->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Only the team captain can unregister the team.');
        }

        $tournament->teamContestants()->detach($team->id);

        return redirect()->back()->with('success', 'Team has been unregistered from this tournament.');
    }

    // Organizer contestant management
    public function manageContestants($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('manageContestants', $tournament);

        // Get pending and approved contestants based on tournament type
        if ($tournament->type === 'individual') {
            $pendingContestants = $tournament->singleContestants()
                ->wherePivot('pending', true)
                ->get();
            $approvedContestants = $tournament->singleContestants()
                ->wherePivot('pending', false)
                ->get();
            $pendingTeams = collect();
            $approvedTeams = collect();
        } else {
            $pendingTeams = $tournament->teamContestants()
                ->wherePivot('pending', true)
                ->get();
            $approvedTeams = $tournament->teamContestants()
                ->wherePivot('pending', false)
                ->get();
            $pendingContestants = collect();
            $approvedContestants = collect();
        }

        // Get bracket data if bracket exists
        $matches = null;
        $rounds = null;
        $maxRound = null;
        if ($tournament->hasBracket()) {
            $matches = $tournament->matches()
                ->orderBy('round')
                ->orderBy('id')
                ->get();
            $rounds = $matches->groupBy('round');
            $maxRound = $rounds->keys()->max();
        }

        return view('tournaments.manage-contestants', compact(
            'tournament',
            'pendingContestants',
            'approvedContestants',
            'pendingTeams',
            'approvedTeams',
            'matches',
            'rounds',
            'maxRound'
        ));
    }

    public function approveContestant($id, $contestantId)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('manageContestants', $tournament);

        if ($tournament->type === 'individual') {
            $tournament->singleContestants()->updateExistingPivot($contestantId, ['pending' => false]);
        } else {
            // Check for player conflicts before approving team
            $teamToApprove = Team::findOrFail($contestantId);

            // Get all players from the team being approved
            $teamPlayerIds = $teamToApprove->players()->pluck('users.id')->toArray();
            $teamPlayerIds[] = $teamToApprove->user_id; // Add captain
            $teamPlayerIds = array_unique($teamPlayerIds);

            // Get all already-approved teams (not including pending ones)
            $approvedTeams = $tournament->teamContestants()
                ->wherePivot('pending', false)
                ->with('players')
                ->get();

            // Check for conflicts
            foreach ($approvedTeams as $approvedTeam) {
                $approvedPlayerIds = $approvedTeam->players()->pluck('users.id')->toArray();
                $approvedPlayerIds[] = $approvedTeam->user_id;
                $approvedPlayerIds = array_unique($approvedPlayerIds);

                $commonPlayers = array_intersect($teamPlayerIds, $approvedPlayerIds);

                if (!empty($commonPlayers)) {
                    $conflictingPlayers = \App\Models\User::whereIn('id', $commonPlayers)->pluck('username')->toArray();
                    $playerNames = implode(', ', $conflictingPlayers);

                    return redirect()->back()->with('error',
                        "Cannot approve team '{$teamToApprove->name}'. The following player(s) are already in approved team '{$approvedTeam->name}': {$playerNames}");
                }
            }

            $tournament->teamContestants()->updateExistingPivot($contestantId, ['pending' => false]);
        }

        return redirect()->back()->with('success', 'Contestant approved successfully.');
    }

    public function rejectContestant($id, $contestantId)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('manageContestants', $tournament);

        if ($tournament->type === 'individual') {
            $tournament->singleContestants()->detach($contestantId);
        } else {
            $tournament->teamContestants()->detach($contestantId);
        }

        return redirect()->back()->with('success', 'Contestant rejected successfully.');
    }

    public function removeContestant($id, $contestantId)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('manageContestants', $tournament);

        // Check if tournament has bracket already
        if ($tournament->hasBracket()) {
            return redirect()->back()->with('error', 'Cannot remove contestants after bracket has been generated.');
        }

        if ($tournament->type === 'individual') {
            $tournament->singleContestants()->detach($contestantId);
        } else {
            $tournament->teamContestants()->detach($contestantId);
        }

        return redirect()->back()->with('success', 'Contestant removed successfully.');
    }

    public function removeTeam($id, $teamId)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('manageContestants', $tournament);

        // Check if tournament has bracket already
        if ($tournament->hasBracket()) {
            return redirect()->back()->with('error', 'Cannot remove teams after bracket has been generated.');
        }

        $tournament->teamContestants()->detach($teamId);

        return redirect()->back()->with('success', 'Team removed successfully.');
    }

    // Admin methods
    public function adminIndex()
    {
        $pendingTournaments = Tournament::where('pending', true)
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.tournaments.index', ['tournaments' => $pendingTournaments]);
    }

    public function approve($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('approve', $tournament);

        $tournament->pending = false;
        $tournament->save();

        return redirect()->route('admin.tournaments')
            ->with('success', 'Tournament "' . $tournament->name . '" has been approved.');
    }

    public function reject($id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('approve', $tournament);

        $tournamentName = $tournament->name;
        $tournament->delete();

        return redirect()->route('admin.tournaments')
            ->with('success', 'Tournament "' . $tournamentName . '" has been rejected and deleted.');
    }

    public function managePrizes($id)
    {
        $tournament = Tournament::with('prizes')->findOrFail($id);
        $this->authorize('update', $tournament);

        return view('tournaments.manage-prizes', ['tournament' => $tournament]);
    }

    public function storePrize(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'prize_index' => 'required|integer|min:1',
            'description' => 'required|string|max:500',
        ]);

        // Check if prize with this index already exists
        $existingPrize = $tournament->prizes()
            ->where('prize_index', $validated['prize_index'])
            ->first();

        if ($existingPrize) {
            return redirect()->back()->with('error', 'A prize with this placement already exists.');
        }

        $tournament->prizes()->create([
            'prize_index' => $validated['prize_index'],
            'description' => $validated['description'],
        ]);

        return redirect()->back()->with('success', 'Prize added successfully!');
    }

    public function updatePrize(Request $request, $id, $prizeIndex)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('update', $tournament);

        $validated = $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $prize = $tournament->prizes()
            ->where('prize_index', $prizeIndex)
            ->firstOrFail();

        $prize->update(['description' => $validated['description']]);

        return redirect()->back()->with('success', 'Prize updated successfully!');
    }

    public function destroyPrize($id, $prizeIndex)
    {
        $tournament = Tournament::findOrFail($id);
        $this->authorize('update', $tournament);

        $tournament->prizes()
            ->where('prize_index', $prizeIndex)
            ->delete();

        return redirect()->back()->with('success', 'Prize removed successfully!');
    }
}