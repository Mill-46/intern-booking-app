<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'approver_id' => User::factory(),
            'level' => 1,
            'status' => 'pending',
            'comment' => null,
            'acted_at' => null,
        ];
    }
}
