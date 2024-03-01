<?php

namespace App\Helpers\TournamentMatchGeneration;

use App\Models\Data\RoundConfiguration;
use App\Models\Enums\TournamentMatchWinner;
use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Facades\Log;

class GroupTournamentMatchGenerator
{
    public function generate(Group $group, Tournament $tournament, int $round)
    {
        $teams = $group->load('teams')->teams;
        $teams->load('homeMatches', 'awayMatches');

        $configuration = collect($tournament->round_settings)->firstWhere('round', '=', $round);

        Log::debug('Configuration: ', $configuration->toArray());

        //Order teams by win count
        $teamsData = $teams->map(function (Team $team) use ($round) {

            $homeWins = $team->homeMatches->where('round', $round)->where('winner', TournamentMatchWinner::HOME_TEAM)->count();
            $awayWins = $team->awayMatches->where('round', $round)->where('winner', TournamentMatchWinner::AWAY_TEAM)->count();

            $homeScores = $team->homeMatches->where('round', $round)->sum('home_team_score');
            $awayScores = $team->awayMatches->where('round', $round)->sum('away_team_score');

            return [
                'team' => $team,
                'win_count' => $homeWins + $awayWins,
                'total_score' => $homeScores + $awayScores
            ];
        });

        Log::debug('Teams data: ', $teamsData->toArray());

        $teamsData = $teamsData->sortBy([
            ['win_count', 'desc'],
            ['total_score', 'desc']
        ]);

        Log::debug('Ordered teams: ', $teamsData->toArray());

        $advancingTeams = $teamsData->take(RoundConfiguration::from($configuration)->advancingCount);

        Log::debug('Advancing teams: ', $advancingTeams->toArray());

        $nextConfiguration = collect($tournament->round_settings)->firstWhere('round', '=', $round + 1);

        if ($nextConfiguration === null)
            return;

        Log::debug('Next configuration: ', $nextConfiguration->toArray());

        $currentGroups = Group::where('tournament_id', $tournament->id)->where('round', $round + 1)->get();

        Log::debug('Initial groups: ', $currentGroups->toArray());

        if ($currentGroups->count() < RoundConfiguration::from($nextConfiguration)->groupCount) {
            $newGroups = $this->createGroups(RoundConfiguration::from($nextConfiguration)->groupCount - $currentGroups->count(), $tournament->id, $round + 1);
            $currentGroups = $currentGroups->merge($newGroups)->unique('id');
        }

        Log::debug('New groups: ', $currentGroups->toArray());

        $currentGroups = $currentGroups->sortBy(function ($group) {
            return $group->load('teams')->teams->count();
        });

        Log::debug('Ordered groups: ', $currentGroups->toArray());

        $advancingTeams->filter(fn(Team $team) => $team->groups->where('round', $round + 1)->count() === 0)->shuffle()->each(function ($team) use (&$currentGroups) {
            $group = $currentGroups->first();
            $group->teams()->attach($team->id);
            $group->load('teams');
            Log::debug('Added team ' . $team->id . ' to group ' . $group->id . ' ' . $group->name);
            //Order groups by team count
            $currentGroups = $currentGroups->sortBy(function ($group) {
                return $group->teams->count();
            });
            Log::debug('Ordered groups: ', $currentGroups->toArray());
        });

        Log::debug('Current groups: ', $currentGroups->toArray());

        $currentGroups->each(function ($group) use ($tournament, $round) {
            $teamIds = $group->teams->pluck('id')->toArray();

            $matches = [];

            for ($i = 0; $i < count($teamIds) - 1; $i++) {
                for ($j = $i + 1; $j < count($teamIds); $j++) {
                    $matches[] = [$teamIds[$i], $teamIds[$j]];
                }
            }

            collect($matches)->each(function ($teams) use ($tournament, $round) {
                TournamentMatch::create([
                    'home_team_id' => $teams[0],
                    'away_team_id' => $teams[1],
                    'round' => $round + 1,
                    'tournament_id' => $tournament->id,
                ]);
            });

            Log::debug('Created matches for group: ' . $group->id . ' ' . $group->name);
            Log::debug('Matches: ' . count($matches));
        });
    }

    public function createGroups(int $groupCount, int $tournamentId, int $round)
    {
        $newGroups = collect();
        for ($i = 0; $i < $groupCount; $i++) {
            $group = Group::create([
                'round' => $round,
                'tournament_id' => $tournamentId,
                'name' => 'Csoport ' . ($round) . '/' . ($i + 1),
                'is_generated' => true
            ]);
            $newGroups->push($group);
        }
        return $newGroups;
    }
}
