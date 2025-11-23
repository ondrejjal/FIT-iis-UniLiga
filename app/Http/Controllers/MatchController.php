<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Matches::with(['tournament', 'user1', 'user2', 'team1', 'team2'])
            ->orderBy('date', 'desc')
            ->orderBy('starting_time', 'desc')
            ->paginate(15);
        
        return view('matches.matches', ['matches' => $matches]);
    }

    public function show($id)
    {
        $match = Matches::with(['tournament', 'user1', 'user2', 'team1', 'team2', 'nextMatch'])
            ->findOrFail($id);
        
        return view('matches.match', ['match' => $match]);
    }

    public function edit($id)
    {
        $match = Matches::with(['tournament', 'user1', 'user2', 'team1', 'team2'])
            ->findOrFail($id);
        
        $tournament = $match->tournament;
        
        // Authorization: Only tournament organizer (not admin)
        if (auth()->id() != $tournament->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        return view('matches.match-edit', ['match' => $match, 'tournament' => $tournament]);
    }

    public function update(Request $request, $id)
    {
        $match = Matches::findOrFail($id);
        $tournament = $match->tournament;
        
        // Authorization: Only tournament organizer (not admin)
        if (auth()->id() != $tournament->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'date' => 'required|date',
            'starting_time' => 'required|date_format:H:i',
            'finishing_time' => 'nullable|date_format:H:i|after:starting_time',
            'result' => 'nullable|string|max:100',
            'winner_user_id' => 'nullable|exists:users,id',
            'winner_team_id' => 'nullable|exists:teams,id',
        ], [
            'finishing_time.after' => 'Finishing time must be after starting time.',
        ]);
        
        // Store old winner before updating
        $oldWinnerUserId = $match->winner_user_id;
        $oldWinnerTeamId = $match->winner_team_id;
        
        $match->date = $validated['date'];
        $match->starting_time = $validated['starting_time'];
        $match->finishing_time = $validated['finishing_time'] ?? null;
        $match->result = $validated['result'] ?? null;
        
        // Set winner based on tournament type
        if ($tournament->type === 'individual') {
            $match->winner_user_id = $validated['winner_user_id'] ?? null;
            $match->winner_team_id = null;
        } else {
            $match->winner_team_id = $validated['winner_team_id'] ?? null;
            $match->winner_user_id = null;
        }
        
        $match->save();
        
        // Handle winner change in bracket
        if ($match->next_match_id) {
            $newWinnerId = $tournament->type === 'individual' ? $match->winner_user_id : $match->winner_team_id;
            $oldWinnerId = $tournament->type === 'individual' ? $oldWinnerUserId : $oldWinnerTeamId;
            
            // If winner changed, clear the old winner from the bracket tree
            if ($oldWinnerId && $oldWinnerId != $newWinnerId) {
                $this->clearContestantFromBranch($match->next_match_id, $oldWinnerId, $tournament->type);
            }
            
            // Advance new winner if set
            if ($newWinnerId) {
                $this->advanceWinner($match);
            }
        }
        
        return redirect()->route('matches.show', $match->id)
            ->with('success', 'Match updated successfully!');
    }

    public function destroy($id)
    {
        $match = Matches::findOrFail($id);
        $tournament = $match->tournament;
        
        // Authorization: Only tournament organizer (not admin)
        if (auth()->id() != $tournament->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        // Don't allow deletion if match has a result or is part of bracket
        if ($match->result) {
            return back()->with('error', 'Cannot delete a match with a result. Clear the result first.');
        }
        
        $tournamentId = $match->tournament_id;
        $match->delete();
        
        return redirect()->route('tournaments.manageContestants', $tournamentId)
            ->with('success', 'Match deleted successfully!');
    }

    /**
     * Advance the winner of a match to the next round
     */
    private function advanceWinner($match)
    {
        if (!$match->next_match_id) {
            return;
        }
        
        $nextMatch = Matches::find($match->next_match_id);
        if (!$nextMatch) {
            return;
        }
        
        // Determine winner based on tournament type
        if ($match->tournament->type === 'individual') {
            $winnerId = $match->winner_user_id;
            if (!$winnerId) {
                return;
            }
            
            // Place winner in next match
            if (!$nextMatch->user1_id) {
                $nextMatch->user1_id = $winnerId;
            } elseif (!$nextMatch->user2_id) {
                $nextMatch->user2_id = $winnerId;
            }
        } else {
            $winnerId = $match->winner_team_id;
            if (!$winnerId) {
                return;
            }
            
            // Place winner in next match
            if (!$nextMatch->team1_id) {
                $nextMatch->team1_id = $winnerId;
            } elseif (!$nextMatch->team2_id) {
                $nextMatch->team2_id = $winnerId;
            }
        }
        
        $nextMatch->save();
        
        Log::info("Advanced winner to next match", [
            'match_id' => $match->id,
            'next_match_id' => $nextMatch->id,
            'winner_id' => $winnerId
        ]);
    }

    /**
     * Clear a contestant from the bracket branch starting from a match
     */
    private function clearContestantFromBranch($matchId, $contestantId, $tournamentType)
    {
        $match = Matches::find($matchId);
        if (!$match) {
            return;
        }
        
        $wasCleared = false;
        
        if ($tournamentType === 'individual') {
            // Clear from user slots
            if ($match->user1_id == $contestantId) {
                $match->user1_id = null;
                $wasCleared = true;
            }
            if ($match->user2_id == $contestantId) {
                $match->user2_id = null;
                $wasCleared = true;
            }
            
            // If this contestant was the winner, clear the winner too
            if ($match->winner_user_id == $contestantId) {
                $match->winner_user_id = null;
                $match->result = null;
            }
        } else {
            // Clear from team slots
            if ($match->team1_id == $contestantId) {
                $match->team1_id = null;
                $wasCleared = true;
            }
            if ($match->team2_id == $contestantId) {
                $match->team2_id = null;
                $wasCleared = true;
            }
            
            // If this contestant was the winner, clear the winner too
            if ($match->winner_team_id == $contestantId) {
                $match->winner_team_id = null;
                $match->result = null;
            }
        }
        
        $match->save();
        
        // If contestant was found and cleared, continue to next match
        if ($wasCleared && $match->next_match_id) {
            $this->clearContestantFromBranch($match->next_match_id, $contestantId, $tournamentType);
        }
        
        Log::info("Cleared contestant from bracket", [
            'match_id' => $matchId,
            'contestant_id' => $contestantId,
            'was_cleared' => $wasCleared
        ]);
    }

    /**
     * Set or update match result
     */
    public function setResult(Request $request, $id)
    {
        $match = Matches::findOrFail($id);
        $tournament = $match->tournament;
        
        // Authorization: Only tournament organizer (not admin)
        if (auth()->id() != $tournament->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if tournament has started
        if ($tournament->start_date && now() < $tournament->start_date) {
            return response()->json(['error' => 'Cannot set match results before the tournament begins.'], 403);
        }
        
        $validated = $request->validate([
            'result' => 'nullable|string|max:100',
            'winner_user_id' => 'nullable|exists:users,id',
            'winner_team_id' => 'nullable|exists:teams,id',
        ]);
        
        // Store old winner before updating
        $oldWinnerUserId = $match->winner_user_id;
        $oldWinnerTeamId = $match->winner_team_id;
        
        $match->result = $validated['result'] ?? null;
        
        // Set winner based on tournament type
        if ($tournament->type === 'individual') {
            $match->winner_user_id = $validated['winner_user_id'] ?? null;
            $match->winner_team_id = null;
        } else {
            $match->winner_team_id = $validated['winner_team_id'] ?? null;
            $match->winner_user_id = null;
        }
        
        $match->save();
        
        // Handle winner change in bracket
        if ($match->next_match_id) {
            $newWinnerId = $tournament->type === 'individual' ? $match->winner_user_id : $match->winner_team_id;
            $oldWinnerId = $tournament->type === 'individual' ? $oldWinnerUserId : $oldWinnerTeamId;
            
            // If winner changed, clear the old winner from the bracket tree
            if ($oldWinnerId && $oldWinnerId != $newWinnerId) {
                $this->clearContestantFromBranch($match->next_match_id, $oldWinnerId, $tournament->type);
            }
            
            // Advance new winner if set
            if ($newWinnerId) {
                $this->advanceWinner($match);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Match result updated successfully!'
        ]);
    }
}