@extends('layouts.app')

@section('title', 'Tournaments')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tournaments</h1>
            <p class="mt-2 text-gray-600">Browse upcoming and past tournaments</p>
        </div>

        <div>
            @auth
                <a href="{{ route('tournaments.create') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
                    Create Tournament
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 font-semibold">
                    Login to Create Tournament
                </a>
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
            {{ session('success') }}
        </div>
    @endif

    @livewire('tournament-search', [
        'perPage' =>
            20
    ])
@endsection