<?php

namespace Platform\Tour\Livewire\Tour;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Tour\Models\Tour;

class Index extends Component
{
    public function render()
    {
        $team = Auth::user()?->currentTeam?->id;

        $tours = $team
            ? Tour::forTeam($team)->withCount('steps')->orderByDesc('id')->get()
            : collect();

        return view('tour::livewire.tour.index', ['tours' => $tours])
            ->layout('platform::layouts.app');
    }
}
