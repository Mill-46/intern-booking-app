<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_no' => strtoupper(fake()->bothify('B####??')),
            'vehicle_type' => fake()->randomElement(['person', 'cargo']),
            'brand' => fake()->company(),
            'model' => fake()->word(),
            'fuel_capacity' => fake()->randomFloat(2, 35, 90),
            'mileage' => fake()->numberBetween(1000, 200000),
            'status' => 'available',
            'owner' => fake()->randomElement(['company', 'rental']),
        ];
    }
}
