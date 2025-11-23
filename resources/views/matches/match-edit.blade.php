@extends('layouts.app')

@section('title', 'Edit Match')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Match</h1>
            <a href="{{ route('tournaments.manageContestants', $match->tournament_id) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                Cancel
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 rounded p-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('matches.update', $match->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4 p-4 bg-gray-100 rounded">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Match Participants</h3>
                <div class="grid grid-cols-2 gap-4">
                    @if($tournament->type === 'individual')
                        <div>
                            <p class="text-sm text-gray-600">Player 1</p>
                            <p class="font-bold">{{ $match->user1->username ?? 'TBD' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Player 2</p>
                            <p class="font-bold">{{ $match->user2->username ?? 'TBD' }}</p>
                        </div>
                    @else
                        <div>
                            <p class="text-sm text-gray-600">Team 1</p>
                            <p class="font-bold">{{ $match->team1->name ?? 'TBD' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Team 2</p>
                            <p class="font-bold">{{ $match->team2->name ?? 'TBD' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <label for="date" class="block text-sm font-bold text-gray-700 mb-1">
                    Match Date *
                </label>
                <input type="date"
                       id="date"
                       name="date"
                       value="{{ old('date', $match->date->format('Y-m-d')) }}"
                       min="{{ $tournament->date->format('Y-m-d') }}"
                       @if($tournament->end_date)
                       max="{{ $tournament->end_date->format('Y-m-d') }}"
                       @endif
                       class="w-full px-3 py-2 border rounded"
                       required>
                <p class="mt-1 text-xs text-gray-600">
                    Match date must be between {{ $tournament->date->format('Y-m-d') }}
                    @if($tournament->end_date)
                        and {{ $tournament->end_date->format('Y-m-d') }}
                    @endif
                </p>
            </div>

            <div class="mb-4">
                <label for="starting_time" class="block text-sm font-bold text-gray-700 mb-1">
                    Starting Time *
                </label>
                <input type="time"
                       id="starting_time"
                       name="starting_time"
                       value="{{ old('starting_time', $match->starting_time->format('H:i')) }}"
                       class="w-full px-3 py-2 border rounded"
                       required>
            </div>

            <div class="mb-4">
                <label for="finishing_time" class="block text-sm font-bold text-gray-700 mb-1">
                    Finishing Time
                </label>
                <input type="time"
                       id="finishing_time"
                       name="finishing_time"
                       value="{{ old('finishing_time', $match->finishing_time ? $match->finishing_time->format('H:i') : '') }}"
                       class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label for="result" class="block text-sm font-bold text-gray-700 mb-1">
                    Match Result/Score
                </label>
                <input type="text"
                       id="result"
                       name="result"
                       value="{{ old('result', $match->result) }}"
                       placeholder="e.g., 3-1, 21-19, etc."
                       maxlength="100"
                       class="w-full px-3 py-2 border rounded">
                <p class="mt-1 text-xs text-gray-600">Optional: Enter the final score or result</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Match Winner
                </label>
                @if($tournament->type === 'individual')
                    <select name="winner_user_id"
                            class="w-full px-3 py-2 border rounded">
                        <option value="">-- No Winner Yet --</option>
                        @if($match->user1)
                            <option value="{{ $match->user1->id }}"
                                    {{ old('winner_user_id', $match->winner_user_id) == $match->user1->id ? 'selected' : '' }}>
                                {{ $match->user1->username }}
                            </option>
                        @endif
                        @if($match->user2)
                            <option value="{{ $match->user2->id }}"
                                    {{ old('winner_user_id', $match->winner_user_id) == $match->user2->id ? 'selected' : '' }}>
                                {{ $match->user2->username }}
                            </option>
                        @endif
                    </select>
                @else
                    <select name="winner_team_id"
                            class="w-full px-3 py-2 border rounded">
                        <option value="">-- No Winner Yet --</option>
                        @if($match->team1)
                            <option value="{{ $match->team1->id }}"
                                    {{ old('winner_team_id', $match->winner_team_id) == $match->team1->id ? 'selected' : '' }}>
                                {{ $match->team1->name }}
                            </option>
                        @endif
                        @if($match->team2)
                            <option value="{{ $match->team2->id }}"
                                    {{ old('winner_team_id', $match->winner_team_id) == $match->team2->id ? 'selected' : '' }}>
                                {{ $match->team2->name }}
                            </option>
                        @endif
                    </select>
                @endif
                <p class="mt-1 text-xs text-gray-600">
                    @if($match->next_match_id)
                        The winner will automatically advance to the next round
                    @else
                        This is the final match
                    @endif
                </p>
            </div>

            <div class="mb-4 p-4 bg-blue-100 rounded">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Tournament</p>
                        <p class="font-bold">{{ $tournament->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Round</p>
                        <p class="font-bold">{{ $match->round ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-bold">
                    Save Changes
                </button>
                <a href="{{ route('tournaments.manageContestants', $match->tournament_id) }}"
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded font-bold text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
