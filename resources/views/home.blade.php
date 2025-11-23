@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="bg-blue-600 rounded-lg shadow p-8 mb-6">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-white">
                Welcome to UniLiga
            </h1>
            <p class="mt-4 text-lg text-white">
                University sports league management system
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4">
            <h3 class="font-bold text-gray-900 mb-2">Manage Teams</h3>
            <p class="text-gray-600 text-sm">
                Create and manage teams, add players and track performance.
            </p>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h3 class="font-bold text-gray-900 mb-2">Track Tournaments</h3>
            <p class="text-gray-600 text-sm">
                Organize tournaments, schedule matches and track standings.
            </p>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Statistics</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['tournaments'] }}</div>
                <div class="text-sm text-gray-600">Tournaments</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['teams'] }}</div>
                <div class="text-sm text-gray-600">Teams</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['matches'] }}</div>
                <div class="text-sm text-gray-600">Matches</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['users'] }}</div>
                <div class="text-sm text-gray-600">Users</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Upcoming Tournaments</h2>
        @if($upcomingTournaments->count() > 0)
            <div class="space-y-4">
                @foreach($upcomingTournaments as $tournament)
                    <div class="relative">
                        <a href="{{ route('tournaments.show', $tournament->id) }}" class="absolute inset-0"></a>
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $tournament->name }}</h3>
                                <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                                    <span>{{ $tournament->date->format('M d, Y') }}</span>
                                    <span>
                                        {{ $tournament->starting_time ? $tournament->starting_time->format('H:i') : 'TBD' }}</span>
                                    @if($tournament->organizer)
                                        <span class="font-semibold">{{ $tournament->organizer->username }}</span>
                                    @endif
                                </div>
                                @if($tournament->description)
                                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $tournament->description }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2 ml-4 z-10">
                                <span
                                    class="px-3 py-1 text-xs font-medium rounded-full
                                                                                  {{ $tournament->type === 'team' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $tournament->type === 'team' ? 'Team' : 'Individual' }}
                                </span>
                                @if($tournament->max_participants)
                                    <span class="text-xs text-gray-500">
                                        Max: {{ $tournament->max_participants }} participants
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No upcoming tournaments scheduled. Check back soon!</p>
            </div>
        @endif
    </div>
@endsection