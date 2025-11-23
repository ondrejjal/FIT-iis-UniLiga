@extends('layouts.app')

@section('title', $tournament->name)

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
    <div class="flex justify-between items-start">
      <div>
        <div class="mb-2">
          <h1 class="text-2xl font-bold text-gray-900 inline">{{ $tournament->name }}</h1>
          <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
            {{ ucfirst($tournament->type) }}
          </span>
          @if($tournament->pending)
          <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
            Pending
          </span>
          @endif
        </div>
        <p class="text-gray-600">Organized by <span
            class="font-medium">{{ $tournament->organizer->username ?? 'Unknown' }}</span></p>
      </div>

      @auth
      @if(auth()->id() === $tournament->user_id)
      <div class="flex gap-2">
        <a href="{{ route('tournaments.manageContestants', $tournament->id) }}"
          class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
          Manage
        </a>
        <a href="{{ route('tournaments.edit', $tournament->id) }}"
          class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          Edit
        </a>
        <form action="{{ route('tournaments.destroy', $tournament->id) }}" method="POST"
          onsubmit="return confirm('Are you sure?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
            Delete
          </button>
        </form>
      </div>
      @endif
      @endauth
    </div>

    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
      <div class="bg-gray-50 rounded p-3">
        <p class="text-xs text-gray-600">Start Date</p>
        <p class="font-bold text-gray-900">{{ $tournament->date->format('M d, Y') }}</p>
      </div>
      @if($tournament->end_date)
      <div class="bg-gray-50 rounded p-3">
        <p class="text-xs text-gray-600">End Date</p>
        <p class="font-bold text-gray-900">{{ $tournament->end_date->format('M d, Y') }}</p>
      </div>
      @endif
      <div class="bg-gray-50 rounded p-3">
        <p class="text-xs text-gray-600">Time</p>
        <p class="font-bold text-gray-900">{{ $tournament->starting_time->format('H:i') }}</p>
      </div>
      <div class="bg-gray-50 rounded p-3">
        <p class="text-xs text-gray-600">Min</p>
        <p class="font-bold text-gray-900">{{ $tournament->min_participants }}</p>
      </div>
      <div class="bg-gray-50 rounded p-3">
        <p class="text-xs text-gray-600">Max</p>
        <p class="font-bold text-gray-900">{{ $tournament->max_participants }}</p>
      </div>
    </div>

    @if($tournament->description)
    <div class="mt-4">
      <h3 class="font-bold text-gray-900 mb-2">Description</h3>
      <p class="text-gray-600 text-sm">{{ $tournament->description }}</p>
    </div>
    @endif
    @auth
    @if(!$tournament->pending && auth()->id() !== $tournament->user_id)
    <div class="mt-4 pt-4 border-t">
      @if($tournament->type === 'individual')
      @if($isRegistered)
      <form action="{{ route('tournaments.unregister', $tournament->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Unregister
        </button>
      </form>
      @elseif(!$isFull)
      <form action="{{ route('tournaments.register', $tournament->id) }}" method="POST">
        @csrf
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Register
        </button>
      </form>
      @else
      <p class="text-gray-500">This tournament is full.</p>
      @endif
      @else
      @if($userTeams->count() > 0)
      @if($availableTeams->count() > 0 && !$isFull)
      <form action="{{ route('tournaments.registerTeam', $tournament->id) }}" method="POST" class="flex gap-2">
        @csrf
        <select name="team_id" required class="px-3 py-2 border rounded">
          <option value="">Select team...</option>
          @foreach($availableTeams as $team)
          <option value="{{ $team->id }}">{{ $team->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Register
        </button>
      </form>
      @elseif($isFull)
      <p class="text-gray-500">This tournament is full.</p>
      @endif

      @if($userRegisteredTeams->count() > 0)
      <div class="mt-3">
        <p class="text-sm text-gray-600 mb-2">Your teams:</p>
        @foreach($userRegisteredTeams as $team)
        <form action="{{ route('tournaments.unregisterTeam', [$tournament->id, $team->id]) }}" method="POST"
          class="inline-block mr-2">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
            Unregister {{ $team->name }}
          </button>
        </form>
        @endforeach
      </div>
      @endif
      @else
      <p class="text-gray-600 text-sm">You need to <a href="{{ route('teams.create') }}" class="text-blue-600">create a
          team</a> first.</p>
      @endif
      @endif
    </div>
    @endif
    @else
    <div class="mt-4 pt-4 border-t">
      <p class="text-gray-600 text-sm">
        <a href="{{ route('login') }}" class="text-blue-600">Login</a> to register.
      </p>
    </div>
    @endauth
  </div>

  @if($user_match)
  <div class="bg-white rounded shadow p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-3">Your Match - Round {{ $user_match->round }}</h2>
        <a href="{{ route('matches.show', $user_match->id) }}">
    <div class="bg-gray-50 rounded p-4">
      @if($tournament->type === 'individual')
      <p class="mb-2 ">
        {{ $user_match->user1 ? $user_match->user1->username : 'TBD' }} :
        {{ $user_match->user2 ? $user_match->user2->username : 'TBD' }}
      </p>
      @elseif($tournament->type === 'team')
      <p class="mb-2">
        {{ $user_match->team1 ? $user_match->team1->name : 'TBD' }} :
        {{ $user_match->team2 ? $user_match->team2->name : 'TBD' }}
      </p>
      @endif
</a>
      <p class="text-gray-500 text-xs mb-2">
        {{$user_match->date->format('M d, Y')}} at {{ $user_match->starting_time->format('H:i') }}
      </p>
    </div>
  </div>
  @endif

  @if($tournament->hasBracket())
  <div class="bg-white rounded shadow p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Bracket</h2>
    <div class="overflow-x-auto">
      <div class="flex gap-8 pb-4 relative" style="min-width: max-content;">
        <!-- SVG for connecting lines -->
        <svg class="absolute top-0 left-0 pointer-events-none z-0 bracket-lines">
          <!-- Lines will be drawn here by JavaScript -->
        </svg>

        @foreach($rounds as $roundNum => $roundMatches)
        <div class="flex-shrink-0 relative z-10" style="width: 220px;">
          <h4 class="text-sm font-semibold text-gray-700 mb-3 text-center">
            @if($roundNum == $maxRound)
            Final
            @elseif($roundNum == $maxRound - 1)
            Semi-Finals
            @elseif($roundNum == $maxRound - 2)
            Quarter-Finals
            @else
            Round {{ $roundNum }}
            @endif
          </h4>

          <div class="flex flex-col relative bracket-round" data-round="{{ $roundNum }}">
            @foreach($roundMatches as $index => $match)
            <a href="{{ route('matches.show', $match->id) }}"
              class="block bg-white border-2 border-gray-300 rounded-lg p-3 text-sm hover:border-blue-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 relative z-10 cursor-pointer"
              data-match-id="{{ $match->id }}" data-round="{{ $roundNum }}" data-index="{{ $index }}">
              @if($tournament->type === 'individual')
              <!-- Player 1 -->
              @if($match->user1_id)
              <div class="mb-2 pb-2 border-b border-gray-300">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-900">
                    {{ $match->user1->username }}
                  </span>
                  @if($match->winner_user_id === $match->user1_id)
                  <span class="text-green-600">🏆</span>
                  @endif
                </div>
              </div>
              @endif

              <!-- Player 2 -->
              @if($match->user2_id)
              <div class="{{ $match->user1_id ? '' : 'mb-2 pb-2 border-b border-gray-300' }}">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-900">
                    {{ $match->user2->username }}
                  </span>
                  @if($match->winner_user_id === $match->user2_id)
                  <span class="text-green-600">🏆</span>
                  @endif
                </div>
              </div>
              @endif

              @if(!$match->user1_id && !$match->user2_id)
              <div class="mb-2 pb-2 border-b border-gray-300">
                <span class="font-medium text-gray-400">TBD</span>
              </div>
              <div>
                <span class="font-medium text-gray-400">TBD</span>
              </div>
              @endif
              @else
              <!-- Team 1 -->
              @if($match->team1_id)
              <div class="mb-2 pb-2 border-b border-gray-300">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-900">
                    {{ $match->team1->name }}
                  </span>
                  @if($match->winner_team_id === $match->team1_id)
                  <span class="text-green-600">🏆</span>
                  @endif
                </div>
              </div>
              @endif

              <!-- Team 2 -->
              @if($match->team2_id)
              <div class="{{ $match->team1_id ? '' : 'mb-2 pb-2 border-b border-gray-300' }}">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-gray-900">
                    {{ $match->team2->name }}
                  </span>
                  @if($match->winner_team_id === $match->team2_id)
                  <span class="text-green-600">🏆</span>
                  @endif
                </div>
              </div>
              @endif

              @if(!$match->team1_id && !$match->team2_id)
              <div class="mb-2 pb-2 border-b border-gray-300">
                <span class="font-medium text-gray-400">TBD</span>
              </div>
              <div>
                <span class="font-medium text-gray-400">TBD</span>
              </div>
              @endif
              @endif

              @if($match->result)
              <div class="mt-2 pt-2 border-t border-gray-300 text-xs text-gray-600">
                <span class="font-semibold">Result:</span> {{ $match->result }}
              </div>
              @endif
              <div class="mt-2 text-xs text-gray-500">
                {{$match->date->format('M j') }} at
                {{$match->starting_time->format('H:i') }}
              </div>
            </a>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const matchConnections = @json($matches -> map(function($match) {
      return [
        'id' => $match -> id,
        'next_match_id' => $match -> next_match_id,
        'position' => $match -> position
      ];
    }) -> values());

    if (typeof window.initBracket === 'function') {
      window.initBracket(matchConnections);
    }
  });
  </script>
  @endpush
  @endif

  @if($tournament->prizes->isNotEmpty())
  <div class="bg-white rounded shadow p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold text-gray-900">Prizes</h2>
      @can('update', $tournament)
      <a href="{{ route('tournaments.managePrizes', $tournament->id) }}"
        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
        Manage
      </a>
      @endcan
    </div>

    <div class="space-y-2">
      @foreach($tournament->prizes as $prize)
      <div class="flex gap-3 p-3 bg-gray-50 rounded">
        <span class="text-2xl">
          @if($prize->prize_index == 1) 🥇
          @elseif($prize->prize_index == 2) 🥈
          @elseif($prize->prize_index == 3) 🥉
          @endif
        </span>
        <div class="flex-1">
          <h3 class="font-semibold text-gray-900 mb-1">
            @if($prize->prize_index == 1) 1st Place
            @elseif($prize->prize_index == 2) 2nd Place
            @elseif($prize->prize_index == 3) 3rd Place
            @else {{ $prize->prize_index }}th Place
            @endif
          </h3>
          <p class="text-gray-700">{{ $prize->description }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @else
  @can('update', $tournament)
  <div class="bg-white rounded shadow p-6 mb-6">
    <p class="text-gray-600 mb-3">No prizes yet.</p>
    <a href="{{ route('tournaments.managePrizes', $tournament->id) }}"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Add Prizes
    </a>
  </div>
  @endcan
  @endif

  <div class="bg-white rounded shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-3">
      {{ $tournament->type === 'team' ? 'Teams' : 'Players' }}
    </h2>

    @if($tournament->type === 'team')
    @if($approvedTeams->count() > 0)
    <div class="space-y-2">
      @foreach($approvedTeams as $team)
      <div class="p-3 bg-gray-50 rounded">
        <a href="{{ route('teams.team.show', $team->id) }}" class="text-blue-600">{{ $team->name }}</a>
      </div>
      @endforeach
    </div>
    @else
    <p class="text-gray-600">No teams yet.</p>
    @endif
    @else
    @if($approvedPlayers->count() > 0)
    <div class="space-y-2">
      @foreach($approvedPlayers as $user)
      <div class="p-3 bg-gray-50 rounded">
        <span>{{ $user->username }}</span>
      </div>
      @endforeach
    </div>
    @else
    <p class="text-gray-600">No players yet.</p>
    @endif
    @endif
  </div>

  <div class="mt-4">
    <a href="{{ route('tournaments.index') }}" class="text-blue-600">
      ← Back to Tournaments
    </a>
  </div>
</div>
@endsection