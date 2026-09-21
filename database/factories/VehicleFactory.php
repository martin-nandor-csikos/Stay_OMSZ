<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_identifier' => $this->faker->unique()->bothify('VEH-##'),
            'plate_number' => strtoupper($this->faker->bothify('MA-##-##')),
            'type' => $this->faker->randomElement(['ESET', 'MGK', 'ROKO']),
            'caregiver_user_id' => null,
            'secondary_caregiver_user_id' => null,
        ];
    }
}
