<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $manager = User::factory()->create([
            'name' => 'Test Manager',
            'email' => 'manager@test.test',
            'role' => 'manager',
        ]);

        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.test',
            'role' => 'admin',
        ]);

        $tournaments = Tournament::factory(5)->create([
            'user_id' => $manager->id,
        ]);

        $startedTournament = Tournament::factory(3)->started()->create([
            'user_id' => $manager->id,
            'ended' => false,
        ]);

        $endedTournament = Tournament::factory(2)->started()->create([
            'user_id' => $manager->id,
            'ended' => true,
        ]);

        $seedWithMatches = $this->command->confirm('Do you want to seed the tournaments with matches? (This willpotentially result in tens of thousands of matches)', false);

        foreach ($tournaments as $tournament) {
            $seedWithTeams = random_int(0, 1);

            if ($seedWithTeams) {
                $tournament->teams()->createMany(
                    Team::factory(random_int(15, 50))->make()->toArray()
                );

                if ($seedWithMatches) {
                    $tournament->matches()->createMany(
                        collect($tournament->teams->pluck('id')->toArray())->crossJoin(
                            $tournament->teams->pluck('id')->toArray()
                        )->map(function ($teams) use ($tournament) {
                            return TournamentMatch::factory()->make([
                                'home_team_id' => $teams[0],
                                'away_team_id' => $teams[1],
                            ]);
                        })->toArray()
                    );
                }
            }
        }

        foreach ($startedTournament as $tournament) {
            $tournament->teams()->createMany(
                Team::factory(random_int(25, 50))->make()->toArray()
            );

            if ($seedWithMatches) {
                $tournament->matches()->createMany(
                    collect($tournament->teams->pluck('id')->toArray())->crossJoin(
                        $tournament->teams->pluck('id')->toArray()
                    )->map(function ($teams) use ($tournament) {
                        return TournamentMatch::factory()->make([
                            'home_team_id' => $teams[0],
                            'away_team_id' => $teams[1],
                        ]);
                    })->toArray()
                );
            }
        }

        foreach ($endedTournament as $tournament) {
            $tournament->teams()->createMany(
                Team::factory(random_int(25, 50))->make()->toArray()
            );

            if ($seedWithMatches) {
                $tournament->matches()->createMany(
                    collect($tournament->teams->pluck('id')->toArray())->crossJoin(
                        $tournament->teams->pluck('id')->toArray()
                    )->map(function ($teams) use ($tournament) {
                        return TournamentMatch::factory()->make([
                            'home_team_id' => $teams[0],
                            'away_team_id' => $teams[1],
                            'status' => 'concluded',
                        ]);
                    })->toArray()
                );
            }
        }
    }
}
