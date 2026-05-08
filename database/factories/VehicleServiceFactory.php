<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleService>
 */
class VehicleServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'service_date' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'service_type' => fake()->randomElement(['Periodic Service', 'Oil Change', 'Brake Check']),
            'workshop_name' => fake()->company() . ' Workshop',
            'cost' => fake()->randomFloat(2, 250000, 3500000),
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
