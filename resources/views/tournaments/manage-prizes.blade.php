@extends('layouts.app')

@section('title', 'Manage Prizes - ' . $tournament->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Manage Prizes</h1>
            <a href="{{ route('tournaments.show', $tournament->id) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                Back to Tournament
            </a>
        </div>

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

        <!-- Add New Prize Form -->
        <div class="mb-8 bg-gray-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Add New Prize</h2>
            <form method="POST" action="{{ route('tournaments.storePrize', $tournament->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="prize_index" class="block text-sm font-medium text-gray-700 mb-2">
                        Placement (1st, 2nd, 3rd, etc.)
                    </label>
                    <input type="number" 
                           id="prize_index" 
                           name="prize_index" 
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Prize Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="e.g., $1000 cash prize, Trophy and medal, etc."
                              required></textarea>
                </div>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    Add Prize
                </button>
            </form>
        </div>

        <!-- Existing Prizes List -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Prizes</h2>
            
            @if($tournament->prizes->isEmpty())
                <p class="text-gray-500 text-center py-8">No prizes added yet.</p>
            @else
                <div class="space-y-4">
                    @foreach($tournament->prizes as $prize)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded">
                                            @if($prize->prize_index == 1) 🥇 1st Place
                                            @elseif($prize->prize_index == 2) 🥈 2nd Place
                                            @elseif($prize->prize_index == 3) 🥉 3rd Place
                                            @else {{ $prize->prize_index }}th Place
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <form method="POST" 
                                          action="{{ route('tournaments.updatePrize', [$tournament->id, $prize->prize_index]) }}"
                                          class="mb-2"
                                          id="edit-form-{{ $prize->prize_index }}">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="description" 
                                                  rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                  required>{{ $prize->description }}</textarea>
                                    </form>
                                </div>
                                
                                <div class="flex gap-2 ml-4">
                                    <button type="submit" 
                                            form="edit-form-{{ $prize->prize_index }}"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm">
                                        Save
                                    </button>
                                    <form method="POST" 
                                          action="{{ route('tournaments.destroyPrize', [$tournament->id, $prize->prize_index]) }}"
                                          onsubmit="return confirm('Are you sure you want to remove this prize?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition text-sm">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection