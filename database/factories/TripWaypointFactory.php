<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripWaypoint>
 */
class TripWaypointFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'           => Str::uuid()->toString(),
            'trip_id'      => Trip::factory(),
            'sequence'     => fake()->numberBetween(0, 20),
            'display_name' => fake()->city() . ', ' . fake()->country(),
            'lat'          => fake()->latitude(),
            'lng'          => fake()->longitude(),
            'provider'     => fake()->optional(0.5)->randomElement(['google', 'mapbox', 'osm']),
            'place_id'     => fake()->optional(0.3)->uuid(),
            'country_code' => fake()->countryCode(),
            'date_from'    => fake()->optional(0.6)->dateTimeBetween('-1 year'),
            'date_to'      => fake()->optional(0.4)->dateTimeBetween('-6 months'),
        ];
    }
}
