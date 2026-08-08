<?php

use Illuminate\Support\Facades\Route;
use Platform\Tour\Livewire\Dashboard;
use Platform\Tour\Livewire\Tour\Index as TourIndex;

Route::get('/', Dashboard::class)->name('tour.dashboard');
Route::get('/tours', TourIndex::class)->name('tour.tours.index');
