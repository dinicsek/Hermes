<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $memberCount = rand(1, 5); // Generate a random number between 1 and 5
        $members = [];

        for ($i = 0; $i < $memberCount; $i++) {
            $members[] = $this->faker->name;
        }

        $emails = [];
        $emailCount = rand(1, count($members)); // Generate a random number between 1 and the number of members

        for ($i = 0; $i < $emailCount; $i++) {
            $emails[] = $this->faker->unique()->safeEmail;
        }

        return [
            'name' => $this->faker->words(3, true),
            'members' => $members,
            'emails' => $emails,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
