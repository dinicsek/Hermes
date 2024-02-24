<?php

namespace App\Livewire;

use App\Data\TournamentData;
use App\Models\Enums\EventStatus;
use App\Models\Tournament;
use Filament\Pages\SimplePage;

class UpcomingTournamentPage extends SimplePage
{
    protected static string $view = 'livewire.upcoming-tournament-page';

    public TournamentData $tournamentData;

    public function mount(Tournament $tournament): void
    {
        if ($tournament->status === EventStatus::UPCOMING && $tournament->registration_starts_at->isPast() && $tournament->registration_ends_at->isFuture()) {
            redirect()->route('register-for-tournament', $tournament);
        } elseif ($tournament->status === EventStatus::ONGOING) {
            redirect()->route('ongoing-tournament', $tournament);
        }

        $this->tournamentData = TournamentData::from($tournament);
    }

    public function getTitle(): string
    {
        return $this->tournamentData->name;
    }

    public function getSubheading(): string
    {
        return 'A verseny ' . $this->tournamentData->starts_at->format('Y. m. d. H:i') . '-kor kezdődik. Regisztrálni ' . ($this->tournamentData->registration_starts_at->isPast() ? 'már nem lehet.' : ' még nem lehet. A regisztráció ' . $this->tournamentData->registration_starts_at->format('Y. m. d. H:i') . '-tól ' . $this->tournamentData->registration_ends_at->format('Y. m. d. H:i') . '-ig lehetséges.');
    }
}
