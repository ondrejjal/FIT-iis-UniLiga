@extends('layouts.app')

@section('title', 'Edit Team')

@section('content')
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Edit Team: {{ $team->name }}</h1>
      <p class="mt-2 text-gray-600">Update team information and manage players</p>
    </div>

    @if ($errors->any())
      <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('success'))
      <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
        {{ session('success') }}
      </div>
    @endif

    <!-- Team Information Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Team Information</h2>

      <form action="{{ route('teams.update', $team->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Team Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Team Name <span class="text-red-500">*</span>
          </label>
          <input type="text" id="name" name="name" value="{{ old('name', $team->name) }}" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
          @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <!-- Team Logo -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Team Logo
          </label>
          @if($team->logo)
            <div class="mb-3">
              <img src="{{ $team->logo_url }}" alt="{{ $team->name }} logo"
                class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">
            </div>
          @else
            <div class="mb-3 w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-sm">
              No Logo
            </div>
          @endif
          <div class="mt-2">
            <input id="logo" name="logo" type="file" accept="image/*"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('logo') border-red-300 @enderror">
          </div>
          <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
          @error('logo')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
          <a href="{{ route('teams.team.show', $team->id) }}"
            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">
            Cancel
          </a>
          <button type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
            Update Team Info
          </button>
        </div>
      </form>
    </div>

    <!-- Team Players Management -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Team Players</h2>

      {{-- Current players (no buttons) --}}
      @livewire('user-search', [
        'teamId' => $team->id,
        'showSearch' => false,
        'mode' => 'remove',
        'userRedirect' =>
          'users.user.show'
      ])

      {{-- Search & Add Players (with Add buttons) --}}
      <div class="mt-6 pt-6 border-t border-gray-200">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Search & Add Players</h3>
        @livewire('user-search', [
          'teamId' => $team->id,
          'showSearch' => true,
          'perPage' => 3,
          'mode' => 'add',
          'userRedirect' => 'users.user.show'
        ])
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow-md p-6 border-2 border-red-200">
      <h2 class="text-xl font-semibold text-red-900 mb-4">Danger Zone</h2>
      <p class="text-gray-600 mb-4">Once you delete a team, there is no going back. Please be certain.</p>
      <form action="{{ route('teams.destroy', $team->id) }}" method="POST"
        onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone!');">
        @csrf
        @method('DELETE')
        <button type="submit"
          class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 font-semibold">
          Delete Team
        </button>
      </form>
    </div>
  </div>
@endsection