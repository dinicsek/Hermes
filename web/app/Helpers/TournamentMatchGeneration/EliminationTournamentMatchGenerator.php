<?php

namespace App\Helpers\TournamentMatchGeneration;

use App\Models\Data\RoundConfiguration;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Database\Eloquent\Builder;
use Log;

class EliminationTournamentMatchGenerator
{
    public function generate(Tournament $tournament, Team $team, int $round, int $eliminationRound, int $newEliminationLevel)
    {
        Log::debug('Generating match for team ' . $team->id);
        Log::debug('Round: ' . $round);
        Log::debug('Elimination round: ' . $eliminationRound);
        Log::debug('Configuration: ', $tournament->round_settings->toArray());
        $configuration = collect($tournament->round_settings)->firstWhere('round', '=', $round);
        Log::debug('Configuration: ', $configuration);

        if (RoundConfiguration::from($configuration)->eliminationLevels < $newEliminationLevel) {
            return;
        }

        $match = $this->getOpenMatches($tournament, $team, $round, $eliminationRound, $newEliminationLevel);
        Log::debug('Open Match: ', $match?->toArray() ?? []);

        if ($match === null) {
            TournamentMatch::create([
                'tournament_id' => $tournament->id,
                'round' => $round,
                'elimination_round' => $eliminationRound + 1,
                'elimination_level' => $newEliminationLevel,
                'home_team_id' => $team->id
            ]);
            Log::debug('Created open match for team ' . $team->id);
            Log::debug('Round: ' . $round);
            Log::debug('Elimination round: ' . $eliminationRound + 1);
            Log::debug('Elimination level: ' . $newEliminationLevel);
        } else {
            $match->away_team_id = $team->id;
            $match->save();
            Log::debug('Match ' . $match->id . ' updated with away team ' . $team->id);
        }
    }

    private function getOpenMatches(Tournament $tournament, Team $team, int $round, int $eliminationRound, int $newEliminationLevel)
    {
        return $tournament->matches()->where('round', $round)->where('elimination_round', $eliminationRound + 1)->where('elimination_level', $newEliminationLevel)->where(function (Builder $query) use ($team) {
            $query->whereNull('away_team_id')->whereNot('home_team_id', $team->id);
        })->first();
    }
}
