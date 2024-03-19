<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Widgets;

use App\Data\TournamentMatchData;
use App\Events\CurrentMatchUpdatedEvent;
use App\Helpers\TournamentMatchGeneration\Jobs\GenerateUpcomingTournamentMatchesJob;
use App\Jobs\SendUpcomingMatchNotificationJob;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;

class ManageTournamentMatch extends Widget
{
    protected static string $view = 'filament.manager.resources.tournament-match-resource.widgets.manage-tournament-match';
    public ?TournamentMatchData $currentTournamentMatchData = null;
    public ?int $tournament_id = null;
    public ?TournamentMatchData $nextTournamentMatchData;

    protected int|string|array $columnSpan = 'full';

    public function mount()
    {
        $this->fetchCurrentMatch();
        if ($this->currentTournamentMatchData !== null && $this->currentTournamentMatchData->started_at !== null && $this->currentTournamentMatchData->ended_at === null) {
            Cache::set('tournament.' . $this->currentTournamentMatchData->tournament_code . '.current-match', $this->currentTournamentMatchData->toArray());
            CurrentMatchUpdatedEvent::broadcast($this->currentTournamentMatchData);
        }
    }

    protected function fetchCurrentMatch()
    {
        $matches = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereEndedAt(null)->whereNotNull(['home_team_id', 'away_team_id'])->with('awayTeam', 'homeTeam')->take(2)->get();

        $match = $matches->first();

        if ($match == null)
            $match = TournamentMatch::ordered()->when(fn() => $this->tournament_id !== null && $this->tournament_id !== 0, fn(Builder $query) => $query->whereTournamentId($this->tournament_id))->whereNotNull(['home_team_id', 'away_team_id'])->latest('sort')->with('awayTeam', 'homeTeam')->first();

        if ($match == null) {
            $this->currentTournamentMatchData = null;
            return;
        }

        $this->currentTournamentMatchData = TournamentMatchData::from(collect($match)->merge([
            'home_team_name' => $match->homeTeam->name,
            'away_team_name' => $match->awayTeam->name,
            'tournament_name' => $match->tournament->name,
            'tournament_code' => $match->tournament->code,
            'home_team_score' => $match->home_team_score ?? 0,
            'away_team_score' => $match->away_team_score ?? 0,
        ]));

        $nextMatch = $matches->last();

        if ($nextMatch == null) {
            $this->nextTournamentMatchData = null;
            return;
        }

        $this->nextTournamentMatchData = TournamentMatchData::from(collect($nextMatch)->merge([
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
        SendUpcomingMatchNotificationJob::dispatch($this->currentTournamentMatchData);
    }

    protected function updateCurrentMatch($data): void
    {
        $tournamentDataArray = $this->currentTournamentMatchData->toArray();

        $newTournamentDataArray = array_merge($tournamentDataArray, $data);
        $this->currentTournamentMatchData = TournamentMatchData::from($newTournamentDataArray);

        $this->dispatch('match-changed');
        CurrentMatchUpdatedEvent::broadcast($this->currentTournamentMatchData);

        Cache::set('tournament.' . $this->currentTournamentMatchData->tournament_code . '.current-match', $this->currentTournamentMatchData->toArray());
        TournamentMatch::where('id', $this->currentTournamentMatchData->id)->update(array_merge(['home_team_score' => $this->currentTournamentMatchData->home_team_score, 'away_team_score' => $this->currentTournamentMatchData->away_team_score], $data));
    }

    public function endMatch(): void
    {
        $this->updateCurrentMatch([
            'ended_at' => now(),
            'winner' => $this->currentTournamentMatchData->home_team_score === $this->currentTournamentMatchData->away_team_score ?
                null : ($this->currentTournamentMatchData->home_team_score > $this->currentTournamentMatchData->away_team_score ? TournamentMatchWinner::HOME_TEAM : TournamentMatchWinner::AWAY_TEAM)
        ]);
        Cache::forget('tournament.' . $this->currentTournamentMatchData->tournament_code . '.current-match');
        GenerateUpcomingTournamentMatchesJob::dispatch($this->currentTournamentMatchData);
    }

    public function incrementHomeTeamScore(): void
    {
        $this->updateCurrentMatch(['home_team_score' => $this->currentTournamentMatchData->home_team_score + 1]);
    }

    public function decrementHomeTeamScore(): void
    {
        if ($this->currentTournamentMatchData->home_team_score <= 0) {
            return;
        }

        $this->updateCurrentMatch(['home_team_score' => $this->currentTournamentMatchData->home_team_score - 1]);
    }

    public function incrementAwayTeamScore(): void
    {
        $this->updateCurrentMatch(['away_team_score' => $this->currentTournamentMatchData->away_team_score + 1]);
    }

    public function decrementAwayTeamScore(): void
    {
        if ($this->currentTournamentMatchData->away_team_score <= 0) {
            return;
        }

        $this->updateCurrentMatch(['away_team_score' => $this->currentTournamentMatchData->away_team_score - 1]);
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
