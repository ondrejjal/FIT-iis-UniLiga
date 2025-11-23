<div>
  @if(isset($user_matches))
    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <div class="max-h-96 overflow-y-auto">
        @if(count($user_matches) > 0)
          <ul class="divide-y divide-gray-200">
            @foreach($user_matches as $match)
              <li wire:key="match-{{ $match->id }}" class="group relative hover:bg-gray-50 p-4 transition">
                <a href="{{ route('matches.show', $match->id) }}" class="absolute inset-0"></a>
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="flex items-center gap-2 text-sm">
                      @if($match->winner_user_id)
                        @if($match->winner_user_id == $match->user1_id)
                          <span class="font-semibold text-green-600">{{ $match->user1 ? $match->user1->username : 'TBD' }}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span class="font-semibold text-gray-800">{{ $match->user2 ? $match->user2->username : 'TBD' }}</span>
                        @else
                          <span class="font-semibold text-gray-800">{{ $match->user1 ? $match->user1->username : 'TBD' }}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span class="font-semibold text-green-600">{{ $match->user2 ? $match->user2->username : 'TBD' }}</span>
                        @endif
                      @else
                        <span class="font-semibold text-gray-800">{{ $match->user1 ? $match->user1->username : 'TBD' }}</span>
                        <span class="font-semibold text-gray-800">vs</span>
                        <span class="font-semibold text-gray-800">{{ $match->user2 ? $match->user2->username : 'TBD' }}</span>
                      @endif
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                      <span>{{ $match->date->format('M d, Y') }} {{ $match->starting_time->format('H:i') }}</span>
                    </div>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="p-8 text-center text-gray-500">
            <p class="mt-4 text-lg font-medium">No individual matches yet</p>
          </div>
        @endif
      </div>
      @if($user_matches->hasPages())
        <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
          {{ $user_matches->links(data: ['scrollTo' => false]) }}
        </div>
      @endif
    </div>
  @endif

  @if(isset($user_team_matches))
    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <div class="max-h-96 overflow-y-auto">
        @if(count($user_team_matches) > 0)
          <ul class="divide-y divide-gray-200">
            @foreach($user_team_matches as $team_match)
              <li wire:key="team-match-{{ $team_match->id }}" class="group relative hover:bg-gray-50 p-4 transition">
                <a href="{{ route('matches.show', $team_match->id) }}" class="absolute inset-0"></a>
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="flex items-center gap-2 text-sm">
                      @if($team_match->winner_team_id)
                        @if($team_match->winner_team_id == $team_match->team1_id)
                          <span
                            class="font-semibold text-green-600">{{ $team_match->team1 ? $team_match->team1->name : 'TBD' }}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span
                            class="font-semibold text-gray-800">{{ $team_match->team2 ? $team_match->team2->name : 'TBD' }}</span>
                        @else
                          <span
                            class="font-semibold text-gray-800">{{ $team_match->team1 ? $team_match->team1->name : 'TBD'}}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span
                            class="font-semibold text-green-600">{{ $team_match->team2 ? $team_match->team2->name : 'TBD' }}</span>
                        @endif
                      @else
                        <span
                          class="font-semibold text-gray-800">{{ $team_match->team1 ? $team_match->team1->name : 'TBD' }}</span>
                        <span class="font-semibold text-gray-800">vs</span>
                        <span
                          class="font-semibold text-gray-800">{{ $team_match->team2 ? $team_match->team2->name : 'TBD' }}</span>
                      @endif
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                      <span>{{ $team_match->date->format('M d, Y') }}
                        {{ $team_match->starting_time->format('H:i') }}</span>
                    </div>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="p-8 text-center text-gray-500">
            <p class="mt-4 text-lg font-medium">No team matches yet</p>
          </div>
        @endif
      </div>
      @if($user_team_matches->hasPages())
        <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
          {{ $user_team_matches->links(data: ['scrollTo' => false]) }}
        </div>
      @endif
    </div>
  @endif

  @if(isset($team_matches))
    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <div class="max-h-96 overflow-y-auto">
        @if(count($team_matches) > 0)
          <ul class="divide-y divide-gray-200">
            @foreach($team_matches as $match)
              <li wire:key="team-match-{{ $match->id }}" class="group relative hover:bg-gray-50 p-4 transition">
                <a href="{{ route('matches.show', $match->id) }}" class="absolute inset-0"></a>
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="flex items-center gap-2 text-sm">
                      @if($match->winner_team_id)
                        @if($match->winner_team_id == $match->team1_id)
                          <span class="font-semibold text-green-600">{{ $match->team1 ? $match->team1->name : 'TBD' }}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span class="font-semibold text-gray-800">{{ $match->team2 ? $match->team2->name : 'TBD' }}</span>
                        @else
                          <span class="font-semibold text-gray-800">{{ $match->team1 ? $match->team1->name : 'TBD'}}</span>
                          <span class="font-semibold text-gray-800">vs</span>
                          <span class="font-semibold text-green-600">{{ $match->team2 ? $match->team2->name : 'TBD' }}</span>
                        @endif
                      @else
                        <span class="font-semibold text-gray-800">{{ $match->team1 ? $match->team1->name : 'TBD' }}</span>
                        <span class="font-semibold text-gray-800">vs</span>
                        <span class="font-semibold text-gray-800">{{ $match->team2 ? $match->team2->name : 'TBD' }}</span>
                      @endif
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                      <span>{{ $match->date->format('M d, Y') }} {{ $match->starting_time->format('H:i') }}</span>
                    </div>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="p-8 text-center text-gray-500">
            <p class="mt-4 text-lg font-medium">No matches yet</p>
          </div>
        @endif
      </div>
      @if($team_matches->hasPages())
        <div class="mt-4 px-4 py-3 bg-gray-50 border-t border-gray-200">
          {{ $team_matches->links(data: ['scrollTo' => false]) }}
        </div>
      @endif
    </div>
  @endif
</div>