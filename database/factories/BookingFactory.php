<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->addDay();

        return [
            'user_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'origin_site_id' => Site::factory(),
            'destination_site_id' => Site::factory(),
            'approver_l1_id' => User::factory()->state(['role' => User::ROLE_APPROVER_L1]),
            'approver_l2_id' => User::factory()->state(['role' => User::ROLE_APPROVER_L2]),
            'start_at' => $start,
            'end_at' => $start->copy()->addHours(4),
            'destination' => fake()->city(),
            'purpose' => fake()->sentence(),
            'status' => Booking::STATUS_DRAFT,
        ];
    }
}
