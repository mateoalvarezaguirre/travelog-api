<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id'    => Trip::factory(),
            'user_id'    => User::factory(),
            'created_at' => fake()->dateTimeBetween('-6 months'),
        ];
    }
}
