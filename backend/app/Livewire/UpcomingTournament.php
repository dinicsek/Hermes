<?php

namespace App\Livewire;

use App\Data\TournamentData;
use App\Models\Tournament;
use Filament\Pages\SimplePage;

class UpcomingTournament extends SimplePage
{
    protected static string $view = 'livewire.upcoming-tournament';

    public TournamentData $tournamentData;

    public function mount(Tournament $tournament): void
    {
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
