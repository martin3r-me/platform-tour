<?php

namespace Platform\Tour\Livewire\Tour;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Tour\Models\Tour;
use Platform\Tour\Support\TourRunner;

class Index extends Component
{
    /** Tour für den aktuellen User starten und zum ersten Schritt springen. */
    public function start(int $tourId)
    {
        $team = (int) (Auth::user()?->currentTeam?->id ?? 0);
        $tour = $team ? Tour::forTeam($team)->find($tourId) : null;
        if (!$tour) {
            return null;
        }

        $url = TourRunner::start($tour, (int) Auth::id(), $team);

        return $this->redirect($url ?: route('tour.dashboard'), navigate: true);
    }

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
