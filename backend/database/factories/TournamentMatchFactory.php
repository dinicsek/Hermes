<?php

namespace Database\Factories;

use App\Models\Enums\TournamentMatchStatus;
use App\Models\TournamentMatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TournamentMatchFactory extends Factory
{
    protected $model = TournamentMatch::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(array_map(fn($case) => $case->value, TournamentMatchStatus::cases())),
            'round' => $this->faker->numberBetween(1, 8),
            'is_final' => false,
            'winner' => null,
            'sort' => null,
            'stakeless' => false,
            'group' => null,
            'is_advancing' => null,
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
}
