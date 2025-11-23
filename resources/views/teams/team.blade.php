@extends('layouts.app')

@section('title', 'Team Details')

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
          @if($team->logo)
            <img src="{{ $team->logo_url }}" alt="{{ $team->name }} logo"
              class="h-20 w-20 rounded-full border-2 border-gray-300 object-cover">
          @else
            <div class="h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
              {{ strtoupper(substr($team->name, 0, 2)) }}
            </div>
          @endif
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $team->name }}</h1>
          </div>
        </div>
        @can('update', $team)
          <a href="{{ route('teams.team-edit', $team->id) }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit Team
          </a>
        @endcan
      </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Statistics</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 rounded p-4">
          <p class="text-sm text-gray-600">Tournaments</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_tournaments }}</p>
        </div>
        <div class="bg-gray-50 rounded p-4">
          <p class="text-sm text-gray-600">Tournaments Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_tournaments_won }}</p>
        </div>
        <div class="bg-gray-50 rounded p-4">
          <p class="text-sm text-gray-600">Matches</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_matches }}</p>
        </div>
        <div class="bg-gray-50 rounded p-4">
          <p class="text-sm text-gray-600">Matches Won</p>
          <p class="text-2xl font-bold text-gray-900">{{ $total_matches_won }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Team Members</h2>
      @livewire('user-search', [
        'mode' => 'display',
        'teamId' => $team->id,
        'showSearch' => false,
        'userRedirect' => 'users.user.show'
      ])
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Matches</h2>
      <div class="bg-gray-50 rounded p-4">
        @livewire('match-search', ['team_id' => $team->id])
      </div>
    </div>

    <div class="bg-white rounded shadow p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Tournaments</h2>
      <div class="bg-gray-50 rounded p-4">
        @livewire('tournament-search', [
          'mode' => 'team',
          'team_id' => $team->id
        ])
      </div>
    </div>

    @can('viewPending', $team)
      <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Pending Tournaments</h2>
        <div class="bg-gray-50 rounded p-4">
          @livewire('tournament-search', [
            'mode' => 'team-pending-join',
            'team_id' => $team->id
          ])
        </div>
      </div>
    @endcan

    @auth
      @if($isMember && auth()->id() !== $team->user_id)
        <div class="bg-white rounded shadow p-6 mb-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Team Membership</h2>
          <div class="bg-orange-50 rounded p-4 border border-orange-200">
            <p class="text-gray-700 mb-3">You are a member of this team.</p>
            <form action="{{ route('teams.leave', $team->id) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to leave {{ $team->name }}?');">
              @csrf
              <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Leave Team
              </button>
            </form>
          </div>
        </div>
      @endif
    @endauth

    <div class="text-center">
      <a href="{{ route('teams') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
        ← Back to Teams
      </a>
    </div>
  </div>
@endsection
