@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Team</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Team Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Team Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Team Logo (optional) -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                    Team Logo (optional)
                </label>
                <div class="mb-3 w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-sm">
                    No Logo
                </div>
                <input type="file"
                       id="logo"
                       name="logo"
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('logo') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                @error('logo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('teams') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
                    Create Team
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 mb-2">Note:</h3>
        <p class="text-sm text-blue-800">You will be automatically set as the team captain. You can add players to your team after creation.</p>
    </div>
</div>
@endsection
