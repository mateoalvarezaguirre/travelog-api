<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripLocation>
 */
class TripLocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'           => Str::uuid()->toString(),
            'trip_id'      => Trip::factory(),
            'provider'     => fake()->randomElement(['google', 'mapbox', 'osm']),
            'place_id'     => fake()->optional(0.5)->uuid(),
            'display_name' => fake()->city() . ', ' . fake()->country(),
            'lat'          => fake()->latitude(),
            'lng'          => fake()->longitude(),
            'country_code' => fake()->countryCode(),
        ];
    }
}
