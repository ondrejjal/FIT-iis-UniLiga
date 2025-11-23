@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    Edit User
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Update user information for {{ $user->username }}
                </p>
            </div>

            @if($errors->any())
                <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="username"
                            name="username"
                            type="text"
                            required
                            value="{{ old('username', $user->username) }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('username') border-red-300 @enderror"
                        >
                    </div>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="first_name"
                            name="first_name"
                            type="text"
                            required
                            value="{{ old('first_name', $user->first_name) }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('first_name') border-red-300 @enderror"
                        >
                    </div>
                    @error('first_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Surname -->
                <div>
                    <label for="surname" class="block text-sm font-medium text-gray-700">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="surname"
                            name="surname"
                            type="text"
                            required
                            value="{{ old('surname', $user->surname) }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('surname') border-red-300 @enderror"
                        >
                    </div>
                    @error('surname')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            value="{{ old('email', $user->email) }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select
                            id="role"
                            name="role"
                            required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('role') border-red-300 @enderror"
                        >
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Change Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                    <p class="text-sm text-gray-600 mb-4">Leave blank to keep the current password</p>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <div class="mt-1">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 @enderror"
                                placeholder="Minimum 8 characters"
                            >
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="mt-1">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <a
                        href="{{ route('admin.users') }}"
                        class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
