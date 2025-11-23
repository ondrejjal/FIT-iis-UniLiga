@extends('layouts.app')

@section('title', 'Teams')

@section('content')
  <div class="mb-8 flex justify-between items-center">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Teams</h1>
      <p class="mt-2 text-gray-600">Browse and manage university sports teams</p>
    </div>

    <div>
      @auth
        <a href="{{ route('teams.create') }}"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 font-semibold">
          Create Team
        </a>
      @else
        <a href="{{ route('login') }}"
          class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 font-semibold">
          Login to Create Team
        </a>
      @endauth
    </div>
  </div>


  @livewire('team-search', [
    'perPage' =>
      20
  ])

@endsection