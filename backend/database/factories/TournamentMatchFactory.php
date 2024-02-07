<?php

namespace Database\Factories;

use App\Models\TournamentMatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TournamentMatchFactory extends Factory
{
    protected $model = TournamentMatch::class;

    public function definition(): array
    {
        $didStart = random_int(0, 1);

        return [
            'round' => $this->faker->numberBetween(1, 8),
            'started_at' => $didStart ? $this->faker->dateTimeBetween('-2 week', '-1 week') : null,
            'ended_at' => $didStart && random_int(0, 1) ? $this->faker->dateTimeBetween('-1 week') : null,
            'is_final' => false,
            'winner' => null,
            'sort' => null,
            'stakeless' => false,
            'elimination_round' => null,
            'elimination_level' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function final(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_final' => true,
            ];
        });
    }

    public function stakeless(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'stakeless' => true,
            ];
        });
    }
}
