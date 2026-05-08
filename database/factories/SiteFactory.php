<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            Site::TYPE_HEAD_OFFICE,
            Site::TYPE_BRANCH_OFFICE,
            Site::TYPE_MINE_SITE,
        ];

        return [
            'name' => fake()->unique()->city(),
            'site_type' => fake()->randomElement($types),
            'region' => fake()->state(),
        ];
    }
}
