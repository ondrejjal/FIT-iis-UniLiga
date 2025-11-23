@extends('layouts.app')

@section('title', 'Match Details')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Match Details</h1>
                <p class="text-gray-600">
                    <a href="{{ route('tournaments.show', $match->tournament->id) }}"
                       class="text-blue-600 hover:text-blue-800">
                        {{ $match->tournament->name }}
                    </a>
                    @if($match->round)
                        - Round {{ $match->round }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @auth
                    @if(auth()->id() == $match->tournament->user_id)
                        <a href="{{ route('matches.edit', $match->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Edit Match
                        </a>
                        <form method="POST" action="{{ route('matches.destroy', $match->id) }}"
                              onsubmit="return confirm('Are you sure you want to delete this match?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                Delete
                            </button>
                        </form>
                    @endif
                @endauth
                <a href="{{ route('tournaments.show', $match->tournament->id) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Back to Tournament
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            @if($match->tournament->type === 'individual')
                <div class="bg-gray-100 rounded p-4 text-center {{ $match->winner_user_id === $match->user1_id ? 'border-2 border-green-500' : '' }}">
                    <p class="text-sm text-gray-600 mb-2">Player 1</p>
                    @if($match->user1)
                        <p class="text-xl font-bold text-gray-900">
                            {{ $match->user1->username }}
                        </p>
                        @if($match->winner_user_id === $match->user1_id)
                            <p class="mt-2 text-green-600 font-bold">Winner</p>
                        @endif
                    @else
                        <p class="text-xl font-bold text-gray-400">TBD</p>
                    @endif
                </div>

                <div class="flex items-center justify-center">
                    <span class="text-3xl font-bold text-gray-400">VS</span>
                </div>

                <div class="bg-gray-100 rounded p-4 text-center {{ $match->winner_user_id === $match->user2_id ? 'border-2 border-green-500' : '' }}">
                    <p class="text-sm text-gray-600 mb-2">Player 2</p>
                    @if($match->user2)
                        <p class="text-xl font-bold text-gray-900">
                            {{ $match->user2->username }}
                        </p>
                        @if($match->winner_user_id === $match->user2_id)
                            <p class="mt-2 text-green-600 font-bold">Winner</p>
                        @endif
                    @else
                        <p class="text-xl font-bold text-gray-400">TBD</p>
                    @endif
                </div>
            @else
                <div class="bg-gray-100 rounded p-4 text-center {{ $match->winner_team_id === $match->team1_id ? 'border-2 border-green-500' : '' }}">
                    <p class="text-sm text-gray-600 mb-2">Team 1</p>
                    @if($match->team1)
                        <a href="{{ route('teams.team.show', $match->team1->id) }}"
                           class="text-xl font-bold text-gray-900 hover:text-blue-600">
                            {{ $match->team1->name }}
                        </a>
                        @if($match->winner_team_id === $match->team1_id)
                            <p class="mt-2 text-green-600 font-bold">Winner</p>
                        @endif
                    @else
                        <p class="text-xl font-bold text-gray-400">TBD</p>
                    @endif
                </div>

                <div class="flex items-center justify-center">
                    <span class="text-3xl font-bold text-gray-400">VS</span>
                </div>

                <div class="bg-gray-100 rounded p-4 text-center {{ $match->winner_team_id === $match->team2_id ? 'border-2 border-green-500' : '' }}">
                    <p class="text-sm text-gray-600 mb-2">Team 2</p>
                    @if($match->team2)
                        <a href="{{ route('teams.team.show', $match->team2->id) }}"
                           class="text-xl font-bold text-gray-900 hover:text-blue-600">
                            {{ $match->team2->name }}
                        </a>
                        @if($match->winner_team_id === $match->team2_id)
                            <p class="mt-2 text-green-600 font-bold">Winner</p>
                        @endif
                    @else
                        <p class="text-xl font-bold text-gray-400">TBD</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-100 rounded p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Schedule</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-bold">{{ $match->date->format('F j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Start Time:</span>
                        <span class="font-bold">{{ $match->starting_time->format('H:i') }}</span>
                    </div>
                    @if($match->finishing_time)
                        <div class="flex justify-between">
                            <span class="text-gray-600">End Time:</span>
                            <span class="font-bold">{{ $match->finishing_time->format('H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-green-100 rounded p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Result</h3>
                @if($match->result)
                    <p class="text-2xl font-bold text-center">{{ $match->result }}</p>
                @else
                    <p class="text-gray-500 text-center">No result yet</p>
                @endif
            </div>
        </div>

        @if($match->nextMatch)
            <div class="mt-6 p-4 bg-purple-100 rounded">
                <p class="text-sm text-gray-600">
                    Winner advances to:
                    <a href="{{ route('matches.show', $match->nextMatch->id) }}"
                       class="font-bold text-purple-600 hover:text-purple-800">
                        Round {{ $match->nextMatch->round }} -
                        {{ $match->nextMatch->date->format('M j, Y') }}
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
