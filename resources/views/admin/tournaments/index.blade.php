@extends('layouts.app')

@section('title', 'Manage Tournaments')

@section('content')
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Pending Tournaments
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Review and approve tournament requests
                    </p>
                </div>

                @if(session('success'))
                    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tournament
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Organizer
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participants
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tournaments as $tournament)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('tournaments.show', $tournament->id) }}"
                                                class="hover:text-blue-600">
                                                {{ $tournament->name }}
                                            </a>
                                        </div>
                                        @if($tournament->short_description)
                                            <div class="text-sm text-gray-500">
                                                {{ $tournament->short_description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tournament->organizer->username ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 rounded-full {{ $tournament->type === 'team' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                            {{ ucfirst($tournament->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tournament->date->format('M d, Y') }}<br>
                                        <span class="text-xs">{{ $tournament->starting_time->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $tournament->min_participants }} - {{ $tournament->max_participants }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('admin.tournaments.approve', $tournament->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-4">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.tournaments.reject', $tournament->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to reject and delete this tournament?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No pending tournaments
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection