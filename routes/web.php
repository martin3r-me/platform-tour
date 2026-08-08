<?php

use Illuminate\Support\Facades\Route;
use Platform\Tour\Livewire\Dashboard;
use Platform\Tour\Livewire\Tour\Index as TourIndex;

Route::get('/', Dashboard::class)->name('tour.dashboard');
Route::get('/tours', TourIndex::class)->name('tour.tours.index');

// Share-Link: startet die Tour für den eingeloggten Empfänger und leitet zum ersten Schritt.
Route::get('/s/{token}', function (string $token) {
    $tour = \Platform\Tour\Models\Tour::where('share_token', $token)->first();
    abort_unless($tour, 404);

    $userId = (int) auth()->id();
    $teamId = (int) (auth()->user()?->currentTeam?->id ?? 0);
    abort_unless($userId && $teamId, 403);

    $url = \Platform\Tour\Support\TourRunner::start($tour, $userId, $teamId);

    return redirect($url ?: route('tour.dashboard'));
})->name('tour.share.run');
