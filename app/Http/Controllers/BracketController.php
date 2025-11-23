<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Matches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BracketController extends Controller
{
    public function shuffle($id)
    {
        $tournament = Tournament::findOrFail($id);

        // Change organizer_id to user_id
        if (auth()->id() != $tournament->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($tournament->type === 'individual') {
            $contestants = $tournament->singleContestants()->wherePivot('pending', false)->get();
            $shuffled = $contestants->shuffle();
            foreach ($shuffled as $index => $contestant) {
                DB::table('tournament_contestants_single')
                    ->where('tournament_id', $tournament->id)
                    ->where('user_id', $contestant->id)
                    ->update(['order' => $index + 1]);
            }
        } else {
            $contestants = $tournament->teamContestants()->wherePivot('pending', false)->get();
            $shuffled = $contestants->shuffle();
            foreach ($shuffled as $index => $contestant) {
                DB::table('tournament_contestants_teams')
                    ->where('tournament_id', $tournament->id)
                    ->where('team_id', $contestant->id)
                    ->update(['order' => $index + 1]);
            }
        }

        return back()->with('success', 'Contestants shuffled successfully!');
    }

    public function reorder(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        if (auth()->id() !== $tournament->organizer_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order = $request->input('order');

        if ($tournament->type === 'individual') {
            foreach ($order as $index => $userId) {
                DB::table('tournament_contestants_single')
                    ->where('tournament_id', $tournament->id)
                    ->where('user_id', $userId)
                    ->update(['order' => $index + 1]);
            }
        } else {
            foreach ($order as $index => $teamId) {
                DB::table('tournament_contestants_teams')
                    ->where('tournament_id', $tournament->id)
                    ->where('team_id', $teamId)
                    ->update(['order' => $index + 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function generate($id)
    {
        $tournament = Tournament::findOrFail($id);

        // Change organizer_id to user_id
        if (auth()->id() != $tournament->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($tournament->hasBracket()) {
            return back()->with('error', 'Bracket already exists. Clear it first.');
        }

        // Force integer comparison (defensive)
        $minNeeded = (int) $tournament->min_participants;

        $contestants = $tournament->type === 'individual'
            ? $tournament->singleContestants()->wherePivot('pending', false)->orderBy('order')->get()
            : $tournament->teamContestants()->wherePivot('pending', false)->orderBy('order')->get();

        $count = (int) $contestants->count();

        if ($count < $minNeeded) {
            return back()->with('error', "Not enough approved contestants to generate bracket. Need at least {$minNeeded}, have {$count}.");
        }

        $this->generateSingleEliminationBracket($tournament, $contestants);

        return back()->with('success', 'Bracket generated successfully!');
    }

    private function generateSingleEliminationBracket($tournament, $contestants)
    {
        $count = $contestants->count();
        $power = (int) pow(2, ceil(log($count, 2))); // bracket size
        
        // Fill with byes (null placeholders)
        $slots = [];
        foreach ($contestants as $c) { 
            $slots[] = $c; 
        }
        while (count($slots) < $power) { 
            $slots[] = null; 
        }

        // Round 1 matches
        $currentRoundMatches = [];
        for ($i = 0; $i < $power; $i += 2) {
            $c1 = $slots[$i];
            $c2 = $slots[$i + 1];

            if ($c1 && $c2) {
                // Real match (both present)
                $match = $this->createMatch($tournament, $c1, $c2, 1);
                $currentRoundMatches[] = $match;
            } elseif ($c1 || $c2) {
                // Bye: the contestant advances directly (store as null match with the contestant)
                $currentRoundMatches[] = (object)[
                    'is_bye' => true,
                    'contestant' => $c1 ?: $c2
                ];
            } else {
                // Two nulls should not happen; skip
            }
        }

        $round = 2;
        // Build subsequent rounds
        while (count($currentRoundMatches) > 1) {
            $nextRoundMatches = [];
            
            for ($i = 0; $i < count($currentRoundMatches); $i += 2) {
                $a = $currentRoundMatches[$i] ?? null;
                $b = $currentRoundMatches[$i + 1] ?? null;

                if (!$b) {
                    // Odd bye: forward the match/contestant to next round
                    $nextRoundMatches[] = $a;
                    continue;
                }

                // Check if both are byes (direct contestants)
                $aIsBye = is_object($a) && isset($a->is_bye);
                $bIsBye = is_object($b) && isset($b->is_bye);

                if ($aIsBye && $bIsBye) {
                    // Both byes: create match between the two contestants
                    $match = $this->createMatch($tournament, $a->contestant, $b->contestant, $round);
                    $nextRoundMatches[] = $match;
                } elseif ($aIsBye && !$bIsBye) {
                    // A is bye, B is match: create match with A's contestant as one side
                    // B match winner will fill the other side later
                    $match = $this->createMatchWithOneContestant($tournament, $a->contestant, 1, $round);
                    // Link previous match B to this new match
                    $b->update(['next_match_id' => $match->id]);
                    $nextRoundMatches[] = $match;
                } elseif (!$aIsBye && $bIsBye) {
                    // A is match, B is bye: create match with B's contestant as one side
                    $match = $this->createMatchWithOneContestant($tournament, $b->contestant, 2, $round);
                    // Link previous match A to this new match
                    $a->update(['next_match_id' => $match->id]);
                    $nextRoundMatches[] = $match;
                } else {
                    // Both are matches: create empty match that both will feed into
                    $match = $this->createEmptyMatch($tournament, $round);
                    // Link both previous matches to this new match
                    $a->update(['next_match_id' => $match->id]);
                    $b->update(['next_match_id' => $match->id]);
                    $nextRoundMatches[] = $match;
                }
            }
            
            $currentRoundMatches = $nextRoundMatches;
            $round++;
        }
    }

    private function createMatch($tournament, $contestant1, $contestant2, $round)
    {
        // Ensure both contestants present to satisfy check constraint (no empty placeholder rows)
        if (!$contestant1 || !$contestant2) {
            return null;
        }

        $matchData = [
            'tournament_id' => $tournament->id,
            'round' => $round,
            'date' => $tournament->date,
            'starting_time' => $tournament->starting_time,
        ];

        if ($tournament->type === 'individual') {
            $matchData['user1_id'] = $contestant1->id;
            $matchData['user2_id'] = $contestant2->id;
        } else {
            $matchData['team1_id'] = $contestant1->id;
            $matchData['team2_id'] = $contestant2->id;
        }

        return Matches::create($matchData);
    }

    private function createMatchWithOneContestant($tournament, $contestant, $position, $round)
    {
        $matchData = [
            'tournament_id' => $tournament->id,
            'round' => $round,
            'date' => $tournament->date,
            'starting_time' => $tournament->starting_time,
        ];

        if ($tournament->type === 'individual') {
            $matchData[$position === 1 ? 'user1_id' : 'user2_id'] = $contestant->id;
            $matchData[$position === 1 ? 'user2_id' : 'user1_id'] = null;
        } else {
            $matchData[$position === 1 ? 'team1_id' : 'team2_id'] = $contestant->id;
            $matchData[$position === 1 ? 'team2_id' : 'team1_id'] = null;
        }

        return Matches::create($matchData);
    }

    private function createEmptyMatch($tournament, $round)
    {
        $matchData = [
            'tournament_id' => $tournament->id,
            'round' => $round,
            'date' => $tournament->date,
            'starting_time' => $tournament->starting_time,
        ];

        if ($tournament->type === 'individual') {
            $matchData['user1_id'] = null;
            $matchData['user2_id'] = null;
        } else {
            $matchData['team1_id'] = null;
            $matchData['team2_id'] = null;
        }

        return Matches::create($matchData);
    }

    private function setContestantToMatch($match, $contestant, $position)
    {
        if (!$match || !$contestant) {
            return;
        }

        $updateData = [];
        if ($match->tournament->type === 'individual') {
            $updateData[$position === 1 ? 'user1_id' : 'user2_id'] = $contestant->id;
        } else {
            $updateData[$position === 1 ? 'team1_id' : 'team2_id'] = $contestant->id;
        }
        $match->update($updateData);
    }

    public function clear($id)
    {
        $tournament = Tournament::findOrFail($id);

        // Change organizer_id to user_id
        if (auth()->id() != $tournament->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized action.');
        }

        DB::transaction(function() use ($tournament) {
            // Break self-references first (avoid FK RESTRICT)
            Matches::where('tournament_id', $tournament->id)->update(['next_match_id' => null]);
            // Delete all matches
            Matches::where('tournament_id', $tournament->id)->delete();
        });

        // If hasBracket() uses cached relation, refresh model
        $tournament->unsetRelation('matches');

        return back()->with('success', 'Bracket cleared successfully!');
    }
}