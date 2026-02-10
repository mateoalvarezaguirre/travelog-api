<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripRoute>
 */
class TripRouteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id'          => Trip::factory(),
            'distance_meters'  => fake()->numberBetween(1000, 500000),
            'duration_seconds' => fake()->numberBetween(600, 36000),
            'routing_provider' => 'none',
            'checksum'         => Str::random(64),
        ];
    }
}
