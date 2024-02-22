<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Widgets;

use App\Data\TournamentMatchData;
use App\Events\CurrentMatchUpdatedEvent;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

class ManageTournamentMatch extends Widget
{
    protected static string $view = 'filament.manager.resources.tournament-match-resource.widgets.manage-tournament-match';
    public ?TournamentMatchData $currentTournamentData = null;
    public ?int $tournament_id = null;
    public ?TournamentMatchData $nextTournamentData;

    protected int|string|array $columnSpan = 'full';

    public function stuff($event)
    {
        ddd($event);
    }

    public function mount()
    {
        $this->fetchCurrentMatch();
    }

    protected function fetchCurrentMatch()
    {
        $matches = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereEndedAt(null)->whereNotNull(['home_team_id', 'away_team_id'])->with('awayTeam', 'homeTeam')->take(2)->get();

        $match = $matches->first();

        if ($match == null)
            $match = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereNotNull(['home_team_id', 'away_team_id'])->latest('sort')->with('awayTeam', 'homeTeam')->first();

        if ($match == null) {
            $this->currentTournamentData = null;
            return;
        }

        $this->currentTournamentData = TournamentMatchData::from(collect($match)->merge([
            'home_team_name' => $match->homeTeam->name,
            'away_team_name' => $match->awayTeam->name,
            'tournament_name' => $match->tournament->name,
            'tournament_code' => $match->tournament->code,
            'home_team_score' => $match->home_team_score ?? 0,
            'away_team_score' => $match->away_team_score ?? 0,
        ]));

        $nextMatch = $matches->last();

        if ($nextMatch == null) {
            $this->nextTournamentData = null;
            return;
        }

        $this->nextTournamentData = TournamentMatchData::from(collect($nextMatch)->merge([
            'home_team_name' => $nextMatch->homeTeam->name,
            'away_team_name' => $nextMatch->awayTeam->name,
            'tournament_name' => $nextMatch->tournament->name,
            'tournament_code' => $nextMatch->tournament->code,
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
            $this->dispatch('match-changed');
        }
    }

    public function startMatch(): void
    {
        $this->updateCurrentMatch(['started_at' => now()]);
    }

    protected function updateCurrentMatch($data): void
    {
        $tournamentDataArray = $this->currentTournamentData->toArray();

        $newTournamentDataArray = array_merge($tournamentDataArray, $data);
        $this->currentTournamentData = TournamentMatchData::from($newTournamentDataArray);

        $this->dispatch('match-changed');
        CurrentMatchUpdatedEvent::broadcast($this->currentTournamentData);

        TournamentMatch::where('id', $this->currentTournamentData->id)->update(array_merge(['home_team_score' => $this->currentTournamentData->home_team_score, 'away_team_score' => $this->currentTournamentData->away_team_score], $data));
    }

    public function endMatch(): void
    {
        $this->updateCurrentMatch([
            'ended_at' => now(),
            'winner' => $this->currentTournamentData->home_team_score === $this->currentTournamentData->away_team_score ?
                null : ($this->currentTournamentData->home_team_score > $this->currentTournamentData->away_team_score ? TournamentMatchWinner::HOME_TEAM : TournamentMatchWinner::AWAY_TEAM)
        ]);
    }

    public function incrementHomeTeamScore(): void
    {
        $this->updateCurrentMatch(['home_team_score' => $this->currentTournamentData->home_team_score + 1]);
    }

    public function decrementHomeTeamScore(): void
    {
        if ($this->currentTournamentData->home_team_score <= 0) {
            return;
        }

        $this->updateCurrentMatch(['home_team_score' => $this->currentTournamentData->home_team_score - 1]);
    }

    public function incrementAwayTeamScore(): void
    {
        $this->updateCurrentMatch(['away_team_score' => $this->currentTournamentData->away_team_score + 1]);
    }

    public function decrementAwayTeamScore(): void
    {
        if ($this->currentTournamentData->away_team_score <= 0) {
            return;
        }

        $this->updateCurrentMatch(['away_team_score' => $this->currentTournamentData->away_team_score - 1]);
    }

    public function resetHomeTeamScore(): void
    {
        $this->updateCurrentMatch(['home_team_score' => 0]);
    }

    public function resetAwayTeamScore(): void
    {
        $this->updateCurrentMatch(['away_team_score' => 0]);
    }

    public function nextMatch(): void
    {
        $this->fetchCurrentMatch();
        $this->dispatch('clear-timer');
    }
}
