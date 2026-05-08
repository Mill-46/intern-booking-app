<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'license_no' => strtoupper(fake()->bothify('SIM####??')),
            'license_expiry' => now()->addYear(),
            'status' => 'active',
        ];
    }
}
