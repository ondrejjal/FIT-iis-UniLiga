<div>
    <!-- Search Bar -->
    @if($showSearch)
        <div class="mb-4">
            <input type="text" wire:model.live="query" placeholder="Search tournaments by name or description..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    @endif

    <!-- Filters Section -->
    @if($showFilters)
        <div class="mb-6">
            <button wire:click="$toggle('filtersOpen')"
                class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-white/30 transition">
                <span class="font-medium text-gray-700">Filters</span>
                <svg class="w-5 h-5 text-gray-600 transition-transform {{ $filtersOpen ? 'rotate-180' : '' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            @if($filtersOpen)
                <div class="mt-3 p-4 rounded-lg border border-gray-300/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        <!-- Tournament Type Filter -->
                        @if($mode != 'team')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select wire:model.live="filterType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Types</option>
                                    <option value="individual">Individual</option>
                                    <option value="team">Team</option>
                                </select>
                            </div>
                        @endif

                        <!-- Managed Filter -->
                        @if($mode == 'user')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User Role</label>
                                <select wire:model.live="relationship"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="participant">Participant</option>
                                    <option value="organizer">Manager</option>
                                    <option value="all">Any</option>
                                </select>
                            </div>
                        @endif

                        <!-- Date From Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date" wire:model.live="filterDateFrom"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Date To Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date" wire:model.live="filterDateTo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select wire:model.live="sortBy"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="date_asc">Date (Earliest First)</option>
                                <option value="date_desc">Date (Latest First)</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="mt-3 flex justify-end">
                        <button wire:click="clearFilters"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Clear All Filters
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Tournaments List -->
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div class="max-h-96 overflow-y-auto">
            @if(count($tournaments) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($tournaments as $tournament)
                        <li wire:key="tournament-{{ $tournament->id }}" class="p-4 group relative hover:bg-gray-50 transition">
                            <a href="{{ route('tournaments.show', $tournament->id) }}" class="absolute inset-0"></a>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $tournament->name }}</h3>
                                    <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                                        <span>{{ $tournament->date->format('M d, Y') }}</span>
                                        <span>
                                            {{ $tournament->starting_time ? $tournament->starting_time->format('H:i') : 'TBD' }}</span>
                                        @if($tournament->organizer)
                                            <span class="font-semibold">{{ $tournament->organizer->username }}</span>
                                        @endif
                                    </div>
                                    @if($tournament->description)
                                        <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $tournament->description }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2 ml-4 z-10">
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full
                                            {{ $tournament->type === 'team' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $tournament->type === 'team' ? 'Team' : 'Individual' }}
                                    </span>
                                    @if($tournament->max_participants)
                                        <span class="text-xs text-gray-500">
                                            Max: {{ $tournament->max_participants }} participants
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="mt-4 text-lg font-medium">No tournaments found!</p>
                    <p class="mt-2 text-sm">Try adjusting your search or check back later for new tournaments.</p>
                </div>
            @endif
        </div>
        @if($tournaments->hasPages())
            <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $tournaments->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>