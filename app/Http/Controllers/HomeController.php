<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\Matches;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch platform statistics
        $stats = [
            'tournaments' => Tournament::count(),
            'teams' => Team::count(),
            'matches' => Matches::whereNotNull('winner_user_id')->count() + Matches::whereNotNull('winner_team_id')->count(),
            'users' => User::count(),
        ];

        // Fetch upcoming tournaments (tournaments with future dates)
        $upcomingTournaments = Tournament::approved()
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->orderBy('starting_time', 'asc')
            ->take(3)
            ->get();

        return view('home', compact('stats', 'upcomingTournaments'));
    }
}