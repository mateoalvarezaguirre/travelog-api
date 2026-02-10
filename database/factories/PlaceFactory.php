<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => fake()->city(),
            'country'     => fake()->country(),
            'date'        => fake()->optional(0.7)->dateTimeBetween('-2 years'),
            'latitude'    => fake()->latitude(),
            'longitude'   => fake()->longitude(),
            'marker_type' => fake()->randomElement(['visited', 'planned', 'wishlist']),
            'image'       => fake()->optional(0.4)->imageUrl(400, 300, 'city'),
        ];
    }
}
