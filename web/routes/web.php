<?php

use App\Livewire\IndexPage;
use App\Livewire\MobileAppDownloadPage;
use App\Livewire\RegisterForTournamentPage;
use App\Livewire\TournamentsPage;
use App\Livewire\UpcomingTournamentPage;
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

Route::get('/', IndexPage::class);
Route::get('/tournaments', TournamentsPage::class)->name('tournaments');

Route::get('/tournaments/{tournament}/register', RegisterForTournamentPage::class)->name('register-for-tournament');
Route::get('/tournaments/{tournament}/upcoming', UpcomingTournamentPage::class)->name('upcoming-tournament');

Route::get('/mobile-app-download', MobileAppDownloadPage::class)->name('mobile-app-download');
