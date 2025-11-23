<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Team;
use Log;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $team_list = Team::all();
        return view('teams.teams', ["teams" => $team_list]);
    }

    public function show($id)
    {
        $team = Team::findOrFail($id);

        // Check if current user is a member of this team
        $isMember = false;
        if (auth()->check()) {
            $isMember = $team->players()->where('users.id', auth()->id())->exists();
        }

        $statistics = $this->getStatistics($team);

        return view('teams.team', [
            'team' => $team,
            'isMember' => $isMember,
            'total_matches' => $statistics['total_matches'],
            'total_tournaments' => $statistics['total_tournaments'],
            'total_matches_won' => $statistics['total_matches_won'],
            'total_tournaments_won' => $statistics['total_tournaments_won']
        ]);
    }

    /**
     * Calculate team statistics
     */
    private function getStatistics(Team $team)
    {
        try {
            $total_matches = $team->matches()->count();
            $total_tournaments = $team->tournaments()->wherePivot('pending', false)->count();
            $total_matches_won = $team->matches()->where('winner_team_id', $team->id)->count();
            $total_tournaments_won = $team->tournaments()->whereHas('matches', function ($query) use ($team) {
                $query->where('winner_team_id', $team->id)
                    ->whereRaw('round = (SELECT MAX(round) FROM matches WHERE matches.tournament_id = tournaments.id)');
            })->count();
        } catch (\Exception $e) {
            Log::error("Error calculating user statistics: " . $e->getMessage());
            $total_tournaments = 0;
            $total_matches_won = 0;
            $total_tournaments_won = 0;
            $total_matches = 0;
        }

        Log::info("User Statistics", [
            'total_matches' => $total_matches,
            'total_tournaments' => $total_tournaments,
            'total_wins' => $total_matches_won,
            'total_tournaments_won' => $total_tournaments_won
        ]);

        return [
            'total_matches' => $total_matches,
            'total_tournaments' => $total_tournaments,
            'total_matches_won' => $total_matches_won,
            'total_tournaments_won' => $total_tournaments_won
        ];
    }


    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team = new Team();
        $team->user_id = auth()->id();
        $team->name = $request->input('name');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $team->logo = $path;
        }

        $team->save();

        return redirect()->route('teams.team.show', ['id' => $team->id])->with('success', 'Team created successfully!');
    }


    public function edit($id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('update', $team);
        $num_players = $team->players()->count();
        return view('teams.team-edit', ['team' => $team, 'num_players' => $num_players, "players" => $team->players]);
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('update', $team);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);
        $team->name = $validated['name'];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($team->logo && Storage::disk('public')->exists($team->logo)) {
                Storage::disk('public')->delete($team->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $team->logo = $path;
        }

        $team->save();

        return redirect()->route('teams.team.show', ['id' => $team->id])->with('success', 'Team updated successfully!');

    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('delete', $team);
        $team->delete();
        return redirect()->route('teams')->with('success', 'Team deleted successfully!');
    }

    public function leave($id)
    {
        $team = Team::findOrFail($id);
        $user = auth()->user();

        // Check if user is a member of the team
        if (!$team->players()->where('users.id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You are not a member of this team.');
        }

        // Prevent captain from leaving (they should transfer ownership or delete team)
        if ($team->user_id === $user->id) {
            return redirect()->back()->with('error', 'As team captain, you cannot leave the team. Please transfer ownership or delete the team.');
        }

        $team->players()->detach($user->id);

        Log::info('User left team', ['userId' => $user->id, 'teamId' => $id]);

        return redirect()->back()->with('success', 'You have successfully left the team.');
    }

}