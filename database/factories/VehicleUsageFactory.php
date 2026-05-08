<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\Vehicle;
use App\Models\VehicleUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleUsage>
 */
class VehicleUsageFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-20 days', '-1 day');
        $endedAt = (clone $startedAt)->modify('+' . random_int(2, 10) . ' hours');
        $odometerStart = fake()->numberBetween(10000, 250000);
        $odometerEnd = $odometerStart + fake()->numberBetween(10, 350);

        return [
            'booking_id' => Booking::factory(),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'origin_site_id' => Site::factory(),
            'destination_site_id' => Site::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'odometer_start' => $odometerStart,
            'odometer_end' => $odometerEnd,
            'notes' => fake()->sentence(),
        ];
    }
}
