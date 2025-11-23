@extends('layouts.app')

@section('title', 'Matches')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded shadow p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900">All Matches</h1>
        <p class="text-gray-600 mt-2">Browse all scheduled and completed matches</p>
    </div>

    @if($matches->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($matches as $match)
                <div class="bg-white rounded shadow p-6">
                    <div class="mb-4">
                        <a href="{{ route('tournaments.show', $match->tournament_id) }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-bold">
                            {{ $match->tournament->name }}
                        </a>
                        @if($match->round)
                            <span class="ml-2 text-xs text-gray-500">Round {{ $match->round }}</span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-4">
                        @if($match->tournament->type === 'individual')
                            <div>
                                <span class="font-bold {{ $match->user1_id ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $match->user1 ? $match->user1->username : 'TBD' }}
                                </span>
                            </div>
                            <div class="text-center text-gray-400 text-sm">vs</div>
                            <div>
                                <span class="font-bold {{ $match->user2_id ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $match->user2 ? $match->user2->username : 'TBD' }}
                                </span>
                            </div>
                        @else
                            <div>
                                <span class="font-bold {{ $match->team1_id ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $match->team1 ? $match->team1->name : 'TBD' }}
                                </span>
                            </div>
                            <div class="text-center text-gray-400 text-sm">vs</div>
                            <div>
                                <span class="font-bold {{ $match->team2_id ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $match->team2 ? $match->team2->name : 'TBD' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($match->result)
                        <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">
                            <p class="text-sm text-green-800">
                                <span class="font-bold">Result:</span> {{ $match->result }}
                            </p>
                        </div>
                    @else
                        <div class="mb-4 p-3 bg-gray-100 border border-gray-300 rounded">
                            <p class="text-sm text-gray-600">Match not completed</p>
                        </div>
                    @endif>

                    <div class="text-sm text-gray-600 mb-4">
                        <div class="mb-1">
                            {{ $match->date->format('M d, Y') }}
                        </div>
                        <div>
                            {{ $match->starting_time->format('H:i') }}
                            @if($match->finishing_time)
                                - {{ $match->finishing_time->format('H:i') }}
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('matches.show', $match->id) }}"
                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        View Details
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $matches->links() }}
        </div>
    @else
        <div class="bg-white rounded shadow p-12 text-center">
            <h3 class="text-xl font-bold text-gray-900">No matches found</h3>
            <p class="mt-1 text-gray-500">There are no scheduled matches at this time.</p>
        </div>
    @endif
</div>
@endsection
