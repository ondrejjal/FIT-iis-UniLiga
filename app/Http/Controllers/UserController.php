<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use Log;

class UserController extends Controller
{
    /**
     * Public user listing (anyone can view)
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('users.users', ['users' => $users]);
    }

    /**
     * User profile (anyone can view)
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        try {
            $user_teams = $user->teams->merge($user->managedTeams)->unique('id');
            $user_tournaments = $user->approvedSingleTournaments;
            $managed_tournaments = $user->approvedManagedTournaments;
            $user_team_tournaments = $user->approvedTeamTournaments()->get();
            $statistics = $this->getStatistics($user);
        } catch (\Exception $e) {
            Log::error("Error fetching user page");
            $user_teams = collect();
            $user_tournaments = collect();
            $user_team_tournaments = collect();
            $managed_tournaments = collect();
        }

        return view('users.user', [
            'user' => $user,
            'user_teams' => $user_teams,
            'user_tournaments' => $user_tournaments,
            'user_team_tournaments' => $user_team_tournaments,
            'managed_tournaments' => $managed_tournaments,
            'total_single_wins' => $statistics['total_single_wins'],
            'total_team_wins' => $statistics['total_team_wins'],
            'total_single_tournaments' => $statistics['total_single_tournaments'],
            'total_team_tournaments' => $statistics['total_team_tournaments'],
            'total_managed_tournaments' => $statistics['total_managed_tournaments'],
            'total_single_tournaments_won' => $statistics['total_single_tournaments_won'],
            'total_team_tournaments_won' => $statistics['total_team_tournaments_won'],
            'total_wins' => $statistics['total_single_tournaments_won'] + $statistics['total_team_tournaments_won']
        ]);
    }

    /**
     * Calculate user statistics
     */
    private function getStatistics(User $user)
    {
        try {
            $total_single_tournaments = $user->approvedSingleTournaments()->count();
            $total_team_tournaments = $user->approvedTeamTournaments()->count();
            $total_managed_tournaments = $user->approvedManagedTournaments()->count();
            $total_single_wins = $user->matches()->where('winner_user_id', $user->id)->count();
            $total_team_wins = $user->teamMatches()->whereIn('winner_team_id', $user->teams->pluck('id'))->count();
            $total_single_tournaments_won = $user->approvedSingleTournaments()->whereHas('matches', function ($query) use ($user) {
                $query->where('winner_user_id', $user->id)
                    ->whereRaw('round = (SELECT MAX(round) FROM matches WHERE matches.tournament_id = tournaments.id)');
            })->count();

            $total_team_tournaments_won = $user->approvedTeamTournaments()->whereHas('matches', function ($query) use ($user) {
                $query->whereIn('winner_team_id', $user->teams->pluck('id'))
                    ->whereRaw('round = (SELECT MAX(round) FROM matches WHERE matches.tournament_id = tournaments.id)');
            })->count();
        } catch (\Exception $e) {
            Log::error("Error calculating user statistics: " . $e->getMessage());
            $total_single_tournaments = 0;
            $total_team_tournaments = 0;
            $total_managed_tournaments = 0;
            $total_single_wins = 0;
            $total_team_wins = 0;
            $total_single_tournaments_won = 0;
            $total_team_tournaments_won = 0;
        }

        Log::info('User Statistics', [
            'total_single_tournaments' => $total_single_tournaments,
            'total_team_tournaments' => $total_team_tournaments,
            'total_single_wins' => $total_single_wins,
            'total_team_wins' => $total_team_wins,
            'total_managed_tournaments' => $total_managed_tournaments,
            'total_single_tournaments_won' => $total_single_tournaments_won,
            'total_team_tournaments_won' => $total_team_tournaments_won
        ]);

        return [
            'total_single_tournaments' => $total_single_tournaments,
            'total_team_tournaments' => $total_team_tournaments,
            'total_single_wins' => $total_single_wins,
            'total_team_wins' => $total_team_wins,
            'total_managed_tournaments' => $total_managed_tournaments,
            'total_single_tournaments_won' => $total_single_tournaments_won,
            'total_team_tournaments_won' => $total_team_tournaments_won
        ];
    }

    /**
     * Admin user management (admin only)
     */
    public function adminIndex()
    {
        $users = User::paginate(15);
        return view('admin.users.index', ['users' => $users]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the form data
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:admin,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot change your own role from admin to user.']);
        }

        // Update user details
        $user->username = $validated['username'];
        $user->first_name = $validated['first_name'];
        $user->surname = $validated['surname'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}