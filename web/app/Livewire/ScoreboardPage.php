<?php

namespace App\Livewire;

use App\Data\TournamentMatchData;
use App\Models\Enums\EventStatus;
use App\Models\Tournament;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ScoreboardPage extends Component
{
    public ?TournamentMatchData $currentTournamentMatchData = null;
    public string $tournamentCode;

    public function mount(Tournament $tournament)
    {
        if ($tournament->status === EventStatus::UPCOMING && $tournament->registration_starts_at->isPast() && $tournament->registration_ends_at->isFuture()) {
            redirect()->route('register-for-tournament', $tournament);
        } else if ($tournament->status === EventStatus::UPCOMING) {
            redirect()->route('upcoming-tournament', $tournament);
        }

        $this->tournamentCode = $tournament->code;
        $currentMatchArray = Cache::get('tournament.' . $tournament->code . '.current-match');

        if ($currentMatchArray !== null) {
            $this->currentTournamentMatchData = TournamentMatchData::from($currentMatchArray);
        }
    }

    public function render()
    {
        return view('livewire.scoreboard-page');
    }

    public function updateCurrentTournamentMatchData($data)
    {
        $this->currentTournamentMatchData = TournamentMatchData::from($data);
        $this->dispatch('match-changed');
    }

    public function refreshCurrentTournamentMatchData()
    {
        $cachedData = Cache::get('tournament.' . $this->tournamentCode . '.current-match');

        if ($cachedData === null) {
            $this->currentTournamentMatchData = null;
            $this->dispatch('match-changed');
            return;
        }

        $this->currentTournamentMatchData = TournamentMatchData::from($cachedData);
    }
}
