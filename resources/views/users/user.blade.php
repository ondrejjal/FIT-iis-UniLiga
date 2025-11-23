@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
  <div>
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
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          @if($user->profile_picture)
            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->username }}'s profile"
              class="h-20 w-20 rounded-full border-2 border-gray-300 object-cover">
          @else
            <div class="h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
              {{ strtoupper(substr($user->username, 0, 2)) }}
            </div>
          @endif
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->username }}</h1>
            <p class="text-gray-600">{{ $user->first_name }} {{ $user->surname }}</p>
          </div>
        </div>
        @can('update', $user)
          <a href="{{ url('/profile') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit Profile
          </a>
        @endcan
      </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Statistics</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Solo Tournaments Participated</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_single_tournaments }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Team Tournaments Participated</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_team_tournaments }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Tournaments Managed</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_managed_tournaments }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Solo Tournaments Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_single_tournaments_won }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Team Tournaments Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_team_tournaments_won }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Solo Matches Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_single_wins }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Team Matches Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_team_wins }}</p>
        </div>
        <div class="bg-gray-50 rounded p-3">
          <p class="text-xs text-gray-600">Total Wins</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_wins }}</p>
        </div>
      </div>

      @if(count($user_teams) > 0)
        <div class="mt-6">
          <h3 class="text-lg font-bold text-gray-800 mb-3">Team Memberships</h3>
          <div class="flex flex-wrap gap-2">
            @foreach($user_teams as $team)
              <div class="relative inline-flex items-center group">
                <a href="{{ route('teams.team.show', $team->id) }}"
                  class="px-x py-2 bg-blue-600 text-white rounded text-sm font-bold hover:bg-blue-700">
                  @if($team->user_id === $user->id)
                    <span class="ml-2 mr-2 text-yellow-300" title="Team Captain">{{ $team->name }}</span>
                  @else
                    <span class="ml-2 mr-2 text-white">
                      {{ $team->name }}
                    </span>
                  @endif
                </a>
                @can('update', $user)
                  @if($team->user_id !== $user->id)
                    <form action="{{ route('teams.leave', $team->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to leave {{ $team->name }}?');">
                      @csrf
                      <button type="submit" class="ml-2 p-1 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-full"
                        title="Leave team">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                          </path>
                        </svg>
                      </button>
                    </form>
                  @endif
                @endcan
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>


    {{-- Matches --}}
    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Matches</h2>

      <div class="space-y-6">
        <div class="bg-gray-50 rounded p-4">
          <h3 class="text-lg font-bold text-gray-900 mb-3">Individual Matches</h3>
          @livewire('match-search', ['user_id' => $user->id, 'mode' => 'individual', 'perPage' => 20])
        </div>

        <div class="bg-gray-50 rounded p-4">
          <h3 class="text-lg font-bold text-gray-900 mb-3">Team Matches</h3>
          @livewire('match-search', ['user_id' => $user->id, 'mode' => 'team', 'perPage' => 20])
        </div>
      </div>
    </div>

    {{-- Tournaments --}}
    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Tournaments</h2>
      <div class="bg-gray-50 rounded p-4">
        @livewire('tournament-search', [
          'mode' => 'user',
          'user_id' => $user->id
        ])
      </div>
    </div>

    @can('viewPending', $user)
      <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Pending Tournaments Managed</h2>
        <div class="bg-gray-50 rounded p-4">
          @livewire('tournament-search', [
            'user_id' => $user->id,
            'showSearch' => true,
            'showFilters' => true,
            'perPage' => 20,
            'mode' => 'user-pending-manage'
          ])
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-4 mt-6">Pending Tournaments Joined</h2>
        <div class="bg-gray-50 rounded p-4">
          @livewire('tournament-search', [
            'user_id' => $user->id,
            'showSearch' => true,
            'showFilters' => true,
            'perPage' => 20,
            'mode' => 'user-pending-join'
          ])
        </div>
      </div>
    @endcan

    <div class="text-center">
      <a href="{{ route('users') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back to Users
      </a>
    </div>
  </div>
@endsection