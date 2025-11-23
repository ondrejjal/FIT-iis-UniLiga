@extends('layouts.app')

@section('title', 'Create Tournament')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Tournament</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tournaments.store') }}" method="POST">
            @csrf

            <!-- Tournament Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tournament Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
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
                    <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="team" {{ old('type') === 'team' ? 'selected' : '' }}>Team</option>
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
                           value="{{ old('date', $today) }}"
                           required
                           min="{{ $today }}"
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
                           value="{{ old('starting_time', '10:00') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('starting_time') border-red-500 @enderror">
                    @error('starting_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                           value="{{ old('min_participants', 2) }}"
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
                           value="{{ old('max_participants', 16) }}"
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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('tournaments.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
                    Create Tournament
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="font-semibold text-yellow-900 mb-2">Note:</h3>
        <p class="text-sm text-yellow-800">Your tournament will need to be approved by an administrator before it becomes visible to other users.</p>
    </div>
</div>
@endsection
