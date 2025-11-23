<div>

  @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($showSearch)
    <div class="mb-6">
      <input type="text" wire:model.live="query" placeholder="Search users by name or email..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
  @endif

  <div class="border border-gray-200 rounded-lg overflow-hidden">
    <div class="max-h-96 overflow-y-auto">
      <ul class="divide-y divide-gray-200">
        @if($captain)
          <li class="p-4 bg-purple-50 border-b-2 border-purple-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-purple-900">{{ $captain->username }}</h3>
                <p class="text-sm text-purple-700">{{ $captain->first_name }} {{ $captain->surname }}
                </p>
                <p class="text-sm text-purple-600">{{ $captain->email }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-200 text-purple-800">
                  Captain
                </span>
              </div>
            </div>
          </li>
        @endif
        @if(count($users) > 0)
            @foreach($users as $user)
              <li wire:key="user-{{ $user->id }}" class="group relative hover:bg-gray-50 p-4 transition">
                <div class="flex items-center justify-between">
                  @if($userRedirect)
                    <a href="{{ route('users.user.show', $user->id) }}" class="absolute inset-0 "></a>
                  @endif
                  <div>
                    <h3>{{ $user->username }}</h3>
                  </div>
                  <div class="flex items-center gap-2 z-10">
                    <span
                      class="px-3 py-1 text-xs font-medium rounded-full 
                                                                                                                                                                                                                                                                                                                      {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                      {{ ucfirst($user->role) }}
                    </span>
                    @if($mode === 'add' && $teamId)
                      <button type="button" wire:click="addUser({{ $user->id }})"
                        class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition z-10">
                        Add me
                      </button>
                    @elseif($mode === 'remove' && $teamId)
                      <button type="button" wire:click="removeUser({{ $user->id }})"
                        wire:confirm="Are you sure you want to remove {{ $user->username }} from the team?"
                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition0">
                        Remove
                      </button>
                      </form>
                    @endif
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @elseif($showSearch and !$captain)
        <div class="p-8 text-center text-gray-500">
          <p class="mt-4 text-lg font-medium">Sorry, no users found!</p>
          <p class="mt-2 text-sm">Try adjusting your search.</p>
        </div>
      @endif
    </div>
    @if($users->hasPages())
      <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
        {{ $users->links(data: ['scrollTo' => false]) }}
      </div>
    @endif
  </div>
</div>