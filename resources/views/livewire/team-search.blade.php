<div>
    <div class="mb-6">
        <input type="text" wire:model.live="query" placeholder="Search teams by name..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div class="max-h-96 overflow-y-auto">
            @if(count($teams) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($teams as $team)
                        <li wire:key="team-{{ $team->id }}" class="group relative hover:bg-gray-50 p-4 transition">
                            <a href="{{ route('teams.team.show', $team->id) }}" class="absolute inset-0"></a>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3>{{ $team->name }}</h3>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p class="mt-4 text-lg font-medium">Sorry, no teams found!</p>
                    <p class="mt-2 text-sm">Try adjusting your search.</p>
                </div>
            @endif
        </div>
        @if($teams->hasPages())
            <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $teams->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>