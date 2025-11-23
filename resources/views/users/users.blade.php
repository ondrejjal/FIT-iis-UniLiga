@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Users</h1>
        <p class="mt-2 text-gray-600">Browse platform users and their statistics</p>
    </div>

    @livewire('user-search', [
        'mode' => 'display',
        'showSearch' => true,
        'userRedirect' => 'users.user.show',
        'perPage' =>
            20
    ])

@endsection