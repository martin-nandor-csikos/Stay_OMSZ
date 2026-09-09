<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rank>
 */
class RankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'salary' => fake()->numberBetween(5000, 50000),
            'rank_order' => 1,
            'requires_exam' => false,
            'minimum_successful_weeks' => 2,
            'is_leader' => false,
        ];
    }
}
