<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomNumber(1, false),
            'price' => fake()->randomNumber(5, true),
            'diagnosis' => fake()->text(),
            'withWho' => fake()->text(),
            'img' => fake()->text(),
        ];
    }
}
