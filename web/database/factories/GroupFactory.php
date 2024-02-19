<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'is_generated' => $this->faker->boolean(),
            'round' => $this->faker->numberBetween(1, 8),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
