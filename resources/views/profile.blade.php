@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white shadow rounded p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-2">
      Edit Profile
    </h2>
    <p class="text-sm text-gray-600 mb-4">
      Update your info
    </p>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
      {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">
          Profile Picture
        </label>
        @if($user->profile_picture)
          <div class="mb-2">
            <img src="{{ $user->profile_picture_url }}" alt="Current picture"
              class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
          </div>
        @else
          <div class="mb-2 w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            {{ strtoupper(substr($user->username, 0, 2)) }}
          </div>
        @endif
        <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
          class="block w-full text-sm @error('profile_picture') border-red-300 @enderror">
        <p class="mt-1 text-xs text-gray-600">JPG, PNG, GIF up to 2MB</p>
        @error('profile_picture')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="username" class="block text-sm font-bold text-gray-700 mb-1">
          Username
        </label>
        <input id="username" type="text" value="{{ $user->username }}" disabled
          class="w-full px-3 py-2 border rounded bg-gray-100 text-gray-500">
        <p class="mt-1 text-xs text-gray-600">Cannot be changed</p>
      </div>

      <div>
        <label for="first_name" class="block text-sm font-bold text-gray-700 mb-1">
          First Name *
        </label>
        <input id="first_name" name="first_name" type="text" required
          value="{{ old('first_name', $user->first_name) }}"
          class="w-full px-3 py-2 border rounded @error('first_name') border-red-500 @enderror">
        @error('first_name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="surname" class="block text-sm font-bold text-gray-700 mb-1">
          Last Name *
        </label>
        <input id="surname" name="surname" type="text" required value="{{ old('surname', $user->surname) }}"
          class="w-full px-3 py-2 border rounded @error('surname') border-red-500 @enderror">
        @error('surname')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">
          Email *
        </label>
        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
          class="w-full px-3 py-2 border rounded @error('email') border-red-500 @enderror">
        @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1">
          Phone Number
        </label>
        <input id="phone_number" name="phone_number" type="text"
          value="{{ old('phone_number', $user->phone_number) }}"
          class="w-full px-3 py-2 border rounded @error('phone_number') border-red-500 @enderror"
          placeholder="+420 123 456 789">
        @error('phone_number')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div class="border-t pt-4">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Change Password</h3>
        <p class="text-sm text-gray-600 mb-3">Leave blank to keep current password</p>

        <div class="mb-3">
          <label for="current_password" class="block text-sm font-bold text-gray-700 mb-1">
            Current Password
          </label>
          <input id="current_password" name="current_password" type="password"
            class="w-full px-3 py-2 border rounded @error('current_password') border-red-500 @enderror">
          @error('current_password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="mb-3">
          <label for="password" class="block text-sm font-bold text-gray-700 mb-1">
            New Password
          </label>
          <input id="password" name="password" type="password"
            class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror"
            placeholder="Minimum 8 characters">
          @error('password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">
            Confirm New Password
          </label>
          <input id="password_confirmation" name="password_confirmation" type="password"
            class="w-full px-3 py-2 border rounded">
        </div>
      </div>

      <div class="flex justify-between pt-4">
        <a href="{{ route('users.user.show', $user->id) }}"
          class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
          ← Back to Profile
        </a>
        <button type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Changes
        </button>
      </div>
      </form>
    </div>
  </div>
</div>
@endsection