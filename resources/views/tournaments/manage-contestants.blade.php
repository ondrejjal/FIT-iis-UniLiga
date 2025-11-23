@extends('layouts.app')

@section('title', 'Manage Contestants - ' . $tournament->name)

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Contestants</h1>
                <p class="text-gray-600">{{ $tournament->name }}</p>
            </div>
            <a href="{{ route('tournaments.show', $tournament->id) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                Back to Tournament
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contestant Management -->
        <div class="space-y-6">
            @if($tournament->type === 'individual')
                <!-- Pending Registrations -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Pending Registrations
                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                            {{ $pendingContestants->count() }}
                        </span>
                    </h2>

                    @if($pendingContestants->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingContestants as $contestant)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $contestant->username }}</p>
                                        <p class="text-sm text-gray-600">{{ $contestant->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $contestant->email }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('tournaments.approveContestant', ['id' => $tournament->id, 'contestantId' => $contestant->id]) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('tournaments.rejectContestant', ['id' => $tournament->id, 'contestantId' => $contestant->id]) }}"
                                              onsubmit="return confirm('Are you sure you want to reject this contestant?');">
                                            @csrf
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No pending registrations.</p>
                    @endif
                </div>

                <!-- Approved Contestants -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            Approved Players
                            <span class="ml-2 text-sm font-normal text-gray-600">
                                ({{ $approvedContestants->count() }} / {{ $tournament->max_participants }})
                            </span>
                        </h2>
                        @if($approvedContestants->count() >= $tournament->min_participants && !$tournament->hasBracket())
                            <form method="POST" action="{{ route('tournaments.shuffle', $tournament->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                                    🎲 Shuffle
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($approvedContestants->count() > 0)
                        <div id="sortable-contestants" class="space-y-2">
                            @foreach($approvedContestants as $contestant)
                                <div data-id="{{ $contestant->id }}" 
                                     class="contestant-item flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg cursor-move hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-400">⋮⋮</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $contestant->username }}</p>
                                            <p class="text-sm text-gray-600">{{ $contestant->name }}</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('tournaments.removeContestant', ['id' => $tournament->id, 'contestantId' => $contestant->id]) }}"
                                          onsubmit="return confirm('Are you sure you want to remove this player?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition text-sm">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No approved players yet.</p>
                    @endif
                </div>

            @else
                <!-- Pending Team Registrations -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Pending Team Registrations
                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">
                            {{ $pendingTeams->count() }}
                        </span>
                    </h2>

                    @if($pendingTeams->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingTeams as $team)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $team->name }}</p>
                                        <p class="text-sm text-gray-600">Captain: {{ $team->captain->username }}</p>
                                        <p class="text-sm text-gray-500">{{ $team->players->count() }} players</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('tournaments.approveContestant', ['id' => $tournament->id, 'contestantId' => $team->id]) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('tournaments.rejectContestant', ['id' => $tournament->id, 'contestantId' => $team->id]) }}"
                                              onsubmit="return confirm('Are you sure you want to reject this team?');">
                                            @csrf
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No pending team registrations.</p>
                    @endif
                </div>

                <!-- Approved Teams -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            Approved Teams
                            <span class="ml-2 text-sm font-normal text-gray-600">
                                ({{ $approvedTeams->count() }} / {{ $tournament->max_participants }})
                            </span>
                        </h2>
                        @if($approvedTeams->count() >= $tournament->min_participants && !$tournament->hasBracket())
                            <form method="POST" action="{{ route('tournaments.shuffle', $tournament->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                                    🎲 Shuffle
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($approvedTeams->count() > 0)
                        <div id="sortable-contestants" class="space-y-2">
                            @foreach($approvedTeams as $team)
                                <div data-id="{{ $team->id }}" 
                                     class="contestant-item flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg cursor-move hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <span class="text-gray-400">⋮⋮</span>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $team->name }}</p>
                                            <p class="text-sm text-gray-600">Captain: {{ $team->captain->username }}</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('tournaments.removeTeam', ['id' => $tournament->id, 'teamId' => $team->id]) }}"
                                          onsubmit="return confirm('Are you sure you want to remove this team?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition text-sm">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No approved teams yet.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Bracket Actions & Visualization -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bracket Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Bracket Actions</h3>
                @php
                    $approvedCount = $tournament->type === 'individual' ? $approvedContestants->count() : $approvedTeams->count();
                @endphp

                @if(!$tournament->hasBracket() && $approvedCount >= $tournament->min_participants)
                    <form method="POST" action="{{ route('tournaments.generateBracket', $tournament->id) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                            Generate Bracket
                        </button>
                    </form>
                @elseif($tournament->hasBracket())
                    <form method="POST" action="{{ route('tournaments.clearBracket', $tournament->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                                onclick="return confirm('Clear bracket? This deletes all matches.')">
                            Clear Bracket
                        </button>
                    </form>
                @endif
            </div>

            <!-- Bracket Visualization -->
            @if($tournament->hasBracket())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tournament Bracket</h3>

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
                                            <a href="{{ route('matches.edit', $match->id) }}" 
                                               class="block bg-white border-2 border-gray-300 rounded-lg p-3 text-sm hover:border-blue-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 relative z-10 cursor-pointer"
                                               data-match-id="{{ $match->id }}"
                                               data-round="{{ $roundNum }}"
                                               data-index="{{ $index }}">
                                                <div class="mb-2 pb-2 border-b border-gray-300">
                                                    @if($tournament->type === 'individual')
                                                        <span class="font-medium {{ $match->user1_id ? 'text-gray-900' : 'text-gray-400' }}">
                                                            {{ $match->user1 ? $match->user1->username : 'TBD' }}
                                                        </span>
                                                    @else
                                                        <span class="font-medium {{ $match->team1_id ? 'text-gray-900' : 'text-gray-400' }}">
                                                            {{ $match->team1 ? $match->team1->name : 'TBD' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($tournament->type === 'individual')
                                                        <span class="font-medium {{ $match->user2_id ? 'text-gray-900' : 'text-gray-400' }}">
                                                            {{ $match->user2 ? $match->user2->username : 'TBD' }}
                                                        </span>
                                                    @else
                                                        <span class="font-medium {{ $match->team2_id ? 'text-gray-900' : 'text-gray-400' }}">
                                                            {{ $match->team2 ? $match->team2->name : 'TBD' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($match->result)
                                                    <div class="mt-2 pt-2 border-t border-gray-300 text-xs text-gray-600">
                                                        <span class="font-semibold">Result:</span> {{ $match->result }}
                                                    </div>
                                                @endif
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
                        const matchConnections = @json($matches->map(function($match) {
                            return [
                                'id' => $match->id,
                                'next_match_id' => $match->next_match_id,
                                'position' => $match->position
                            ];
                        })->values());
                        
                        if (typeof window.initBracket === 'function') {
                            window.initBracket(matchConnections);
                        }
                    });
                </script>
                @endpush
            @endif
        </div>
    </div>
</div>

@if(!$tournament->hasBracket())
<script>
// Drag and drop functionality using HTML5 Drag & Drop API
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sortable-contestants');
    if (!container) return;

    const items = container.querySelectorAll('.contestant-item');
    let draggedItem = null;

    items.forEach(item => {
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.style.opacity = '1';
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            if (this !== draggedItem) {
                const rect = this.getBoundingClientRect();
                const middle = rect.top + rect.height / 2;
                
                if (e.clientY < middle) {
                    this.parentNode.insertBefore(draggedItem, this);
                } else {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                }
            }
        });
    });

    // Save order when drag ends
    container.addEventListener('dragend', function() {
        const items = container.querySelectorAll('.contestant-item');
        const order = Array.from(items).map(item => item.getAttribute('data-id'));
        
        // Send to server
        fetch('{{ route('tournaments.reorder', $tournament->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: order })
        });
    });
});
</script>
@endif
@endsection
