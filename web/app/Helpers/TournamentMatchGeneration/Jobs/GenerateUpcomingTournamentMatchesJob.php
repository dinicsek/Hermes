<?php

namespace App\Helpers\TournamentMatchGeneration\Jobs;

use App\Data\TournamentMatchData;
use App\Helpers\TournamentMatchGeneration\EliminationTournamentMatchGenerator;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateUpcomingTournamentMatchesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TournamentMatchData $previousMatchData)
    {
    }

    public function handle(): void
    {
        $tournament = Tournament::find($this->previousMatchData->tournament_id);

        $generator = new EliminationTournamentMatchGenerator();

        if ($this->previousMatchData->is_final || $this->previousMatchData->winner === null) {
            return;
        }

        $winnerId = $this->previousMatchData->winner === TournamentMatchWinner::HOME_TEAM ? $this->previousMatchData->home_team_id : $this->previousMatchData->away_team_id;
        $loserId = $this->previousMatchData->winner === TournamentMatchWinner::HOME_TEAM ? $this->previousMatchData->away_team_id : $this->previousMatchData->home_team_id;

        $generator->generate($tournament, Team::find($winnerId), $this->previousMatchData->round, $this->previousMatchData->elimination_round, $this->previousMatchData->elimination_level);
        $generator->generate($tournament, Team::find($loserId), $this->previousMatchData->round, $this->previousMatchData->elimination_round, $this->previousMatchData->elimination_level + 1);
    }
}
