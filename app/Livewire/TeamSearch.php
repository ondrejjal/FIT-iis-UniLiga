<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Team;

class TeamSearch extends Component
{
    use WithPagination;

    public $query = '';
    public $perPage = 5;

    public function render()
    {
        $teams_list = Team::where('name', 'LIKE', "%{$this->query}%")->paginate($this->perPage);

        return view('livewire.team-search', [
            'teams' => $teams_list
        ]);
    }

}