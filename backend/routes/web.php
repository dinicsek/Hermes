<?php

use App\Livewire\Index;
use App\Livewire\RegisterForTournament;
use App\Livewire\Tournaments;
use App\Livewire\UpcomingTournament;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Index::class);
Route::get('/tournaments', Tournaments::class)->name('tournaments');

Route::get('/tournaments/{tournament}/register', RegisterForTournament::class)->name('register-for-tournament');
Route::get('/tournaments/{tournament}/upcoming', UpcomingTournament::class)->name('upcoming-tournament');
