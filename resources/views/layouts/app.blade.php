<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'UniLiga') }} - @yield('title', 'University Sports League')</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 antialiased">
  <!-- Navigation -->
  <nav class="bg-white shadow border-b">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between h-16">
        <div class="flex items-center space-x-8">
          <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">
            UniLiga
          </a>

          <div class="flex space-x-4">
            <a href="{{ url('/tournaments') }}"
              class="px-3 py-2 {{ request()->is('tournaments*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
              Tournaments
            </a>
            <a href="{{ url('/teams') }}"
              class="px-3 py-2 {{ request()->is('teams*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
              Teams
            </a>
            <a href="{{ url('/users') }}"
              class="px-3 py-2 {{ request()->is('users*') ? 'text-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900' }}">
              Users
            </a>
          </div>
        </div>

        <div class="flex items-center">
          @guest
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="px-3 py-2 text-gray-700">
              Account
            </button>

            <div x-show="open" @click.away="open = false"
              class="absolute right-0 mt-2 w-40 bg-white rounded shadow py-1">
              <a href="{{ url('/login') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Login</a>
              <a href="{{ url('/register') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Register</a>
            </div>
          </div>
          @else
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="px-3 py-2 text-gray-700">
              {{ Auth::user()->first_name ?? 'User' }}
            </button>

            <div x-show="open" @click.away="open = false"
              class="absolute right-0 mt-2 w-40 bg-white rounded shadow py-1">
              <a href="{{ route('users.user.show', Auth::id()) }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>

              @if(Auth::user()->role === 'admin')
              <a href="{{ route('admin.users') }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">Manage Users</a>
              <a href="{{ route('admin.tournaments') }}"
                class="block px-4 py-2 text-sm hover:bg-gray-100">Approve Tournaments</a>
              @endif

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                  Logout
                </button>
              </form>
            </div>
          </div>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <main class="py-6">
    <div class="max-w-7xl mx-auto px-4">
      @yield('content')
    </div>
  </main>

  <footer class="bg-white border-t mt-8 py-4">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
      &copy; {{ now()->year }} UniLiga
    </div>
  </footer>

  @livewireScripts
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('scripts')
</body>

</html>