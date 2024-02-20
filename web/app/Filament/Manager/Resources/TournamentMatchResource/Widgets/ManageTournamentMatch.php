<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Widgets;

use App\Data\TournamentMatchData;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

class ManageTournamentMatch extends Widget
{
    protected static string $view = 'filament.manager.resources.tournament-match-resource.widgets.manage-tournament-match';
    public ?TournamentMatchData $currentTournamentData;
    public ?int $tournament_id = null;

    protected int|string|array $columnSpan = 'full';

    public function mount()
    {
        $this->fetchCurrentMatch();
    }

    protected function fetchCurrentMatch()
    {
        $match = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereEndedAt(null)->whereNotNull(['home_team_id', 'away_team_id'])->with('awayTeam', 'homeTeam')->first();

        if ($match == null)
            $match = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereNotNull(['home_team_id', 'away_team_id'])->latest('sort')->with('awayTeam', 'homeTeam')->first();

        if ($match == null) {
            $this->currentTournamentData = null;
            return;
        }

        $this->currentTournamentData = TournamentMatchData::from(collect($match)->merge([
            'home_team_name' => $match->homeTeam->name,
            'away_team_name' => $match->awayTeam->name,
            'tournament_name' => $match->tournament->name
        ]));
    }

    #[Computed]
    public function selectableTournaments()
    {
        return Tournament::whereUserId(auth()->id())->select(['id', 'name'])->get()->map(function (Tournament $tournament) {
            return ['name' => $tournament->name, 'id' => $tournament->id];
        });
    }

    public function updated($property): void
    {
        if ($property === "tournament_id") {
            $this->fetchCurrentMatch();
        }
    }
}
