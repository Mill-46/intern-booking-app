<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\FuelConsumption;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FuelConsumption>
 */
class FuelConsumptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'vehicle_id' => Vehicle::factory(),
            'fuel_used' => fake()->randomFloat(2, 5, 100),
            'recorded_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }
}
