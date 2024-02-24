<?php

namespace App\Helpers\TournamentMatchGeneartion;

use App\Models\Data\RoundConfiguration;
use App\Models\Enums\RoundMode;
use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Collection;
use Log;

class InitialTournamentMatchGenerator
{
    public function generate(Tournament $tournament, array $excludedTeamIds)
    {
        $teams = $this->getTeamsForRound($tournament, 1, $excludedTeamIds);

        $roundSettings = collect($tournament->round_settings)->firstWhere('round', '=', 1);
        if (RoundConfiguration::from($roundSettings)->mode === RoundMode::GROUP) {
            $this->generateGroupMatches($teams, RoundConfiguration::from($roundSettings), $tournament);
        } else if (RoundConfiguration::from($roundSettings)->mode === RoundMode::ELIMINATION) {
            $this->generateEliminationMatches($teams, $tournament);
        }
    }

    private function getTeamsForRound(Tournament $tournament, int $round, array $excludedTeamIds)
    {
        $teams = $tournament->teams()->whereIsApproved(true)->whereNotIn('id', $excludedTeamIds)->with(['groups' => function ($query) use ($round) {
            $query->where('round', $round);
        }])->get();

        return $teams;
    }

    private function generateGroupMatches(Collection $teams, RoundConfiguration $roundSettings, Tournament $tournament)
    {
        $groupCount = $roundSettings->groupCount;

        $currentGroups = $teams->flatMap(function ($team) {
            return $team->groups;
        });

        $currentGroups = $currentGroups->unique('id');
        $currentGroupCount = $currentGroups->count();

        if ($currentGroupCount < $groupCount) {
            $newGroups = $this->createGroups($groupCount - $currentGroupCount, $tournament->id);
            $currentGroups = $currentGroups->merge($newGroups)->unique('id');
        }

        Log::debug('Current groups: ' . $currentGroups->count());
        Log::debug('New current groups: ', $currentGroups->toArray());

        //Order groups by team count
        $currentGroups = $currentGroups->sortBy(function ($group) {
            return $group->load('teams')->teams->count();
        });

        Log::debug('Ordered groups: ', $currentGroups->toArray());

        $teams->filter(fn(Team $team) => $team->groups->where('round', 1)->count() === 0)->shuffle()->each(function ($team) use (&$currentGroups) {
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

        $currentGroups->each(function ($group) use ($tournament) {
            $teamIds = $group->teams->pluck('id')->toArray();

            $matches = [];

            for ($i = 0; $i < count($teamIds) - 1; $i++) {
                for ($j = $i + 1; $j < count($teamIds); $j++) {
                    $matches[] = [$teamIds[$i], $teamIds[$j]];
                }
            }

            collect($matches)->each(function ($teams) use ($tournament) {
                TournamentMatch::create([
                    'home_team_id' => $teams[0],
                    'away_team_id' => $teams[1],
                    'round' => 1,
                    'tournament_id' => $tournament->id,
                ]);
            });

            Log::debug('Created matches for group: ' . $group->id . ' ' . $group->name);
            Log::debug('Matches: ' . count($matches));
        });
    }

    private function createGroups(int $count, int $tournamentId): Collection
    {
        $newGroups = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $newGroups->push(Group::create([
                'name' => 'Csoport 1/' . ($i + 1),
                'tournament_id' => $tournamentId,
                'round' => 1,
                'is_generated' => true,
            ]));
            Log::debug('Created group: ' . $newGroups->last()->id . ' ' . $newGroups->last()->name);
        }
        return $newGroups;
    }

    private function generateEliminationMatches(Collection $teams, Tournament $tournament)
    {
        $teamIds = $teams->pluck('id')->shuffle()->toArray();

        Log::debug('Elimination teams: ', $teamIds);
        
        for ($i = 0; $i < count($teamIds); $i += 2) {
            $match = TournamentMatch::create([
                'home_team_id' => $teamIds[$i],
                'away_team_id' => $teamIds[$i + 1] ?? null,
                'round' => isset($teamIds[$i + 1]) ? 1 : 2,
                'tournament_id' => $tournament->id,
                'elimination_round' => 1,
                'elimination_level' => 1,
            ]);
            Log::debug('Created elimination match: ' . $match->id . ' ' . $match->home_team_id . ' - ' . $match->away_team_id);
        }
    }
}
