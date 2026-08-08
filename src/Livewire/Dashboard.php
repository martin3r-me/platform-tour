<?php

namespace Platform\Tour\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Tour\Models\Tour;

class Dashboard extends Component
{
    public function render()
    {
        $team = Auth::user()?->currentTeam?->id;

        return view('tour::livewire.dashboard', [
            'total'  => $team ? Tour::forTeam($team)->count() : 0,
            'active' => $team ? Tour::forTeam($team)->active()->count() : 0,
        ])->layout('platform::layouts.app');
    }
}
