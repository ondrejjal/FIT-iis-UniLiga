@extends('layouts.app')

@section('title', 'Edit Tournament')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Tournament</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tournaments.update', $tournament->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tournament Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tournament Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $tournament->name) }}"
                       required
                       maxlength="150"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tournament Type -->
            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tournament Type <span class="text-red-500">*</span>
                </label>
                <select id="type"
                        name="type"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                    <option value="individual" {{ old('type', $tournament->type) === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="team" {{ old('type', $tournament->type) === 'team' ? 'selected' : '' }}>Team</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date and Time -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="date"
                           name="date"
                           value="{{ old('date', $tournament->date->format('Y-m-d')) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="starting_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Starting Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time"
                           id="starting_time"
                           name="starting_time"
                           value="{{ old('starting_time', $tournament->starting_time) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('starting_time') border-red-500 @enderror">
                    @error('starting_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- End Date -->
            <div class="mb-6">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                    End Date (Optional)
                </label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ old('end_date', isset($tournament) && $tournament->end_date ? $tournament->end_date->format('Y-m-d') : '') }}"
                       min="{{ old('date', isset($tournament) ? $tournament->date->format('Y-m-d') : date('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">If specified, all matches must be scheduled between start and end dates</p>
            </div>

            <!-- Participants -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="min_participants" class="block text-sm font-medium text-gray-700 mb-2">
                        Min Participants <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="min_participants"
                           name="min_participants"
                           value="{{ old('min_participants', $tournament->min_participants) }}"
                           required
                           min="2"
                           max="256"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('min_participants') border-red-500 @enderror">
                    @error('min_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Participants <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="max_participants"
                           name="max_participants"
                           value="{{ old('max_participants', $tournament->max_participants) }}"
                           required
                           min="2"
                           max="256"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_participants') border-red-500 @enderror">
                    @error('max_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $tournament->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('tournaments.show', $tournament->id) }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
                    Update Tournament
                </button>
            </div>
        </form>
    </div>

    @if($tournament->pending)
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-900 mb-2">Pending Approval</h3>
            <p class="text-sm text-yellow-800">This tournament is still waiting for administrator approval.</p>
        </div>
    @endif
</div>
@endsection
