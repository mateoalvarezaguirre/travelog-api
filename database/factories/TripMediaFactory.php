<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripMedia>
 */
class TripMediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id'     => Trip::factory(),
            'media_type'  => 'image',
            'media_url'   => fake()->imageUrl(800, 600, 'travel'),
            'caption'     => fake()->optional(0.6)->sentence(),
            'order'       => fake()->numberBetween(0, 10),
            'is_featured' => fake()->boolean(20),
            'is_visible'  => true,
            'uploaded_by' => User::factory(),
        ];
    }
}
