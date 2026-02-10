<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripComment>
 */
class TripCommentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'      => Str::uuid()->toString(),
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'text'    => fake()->paragraph(),
        ];
    }
}
