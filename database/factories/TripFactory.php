<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = '<p>' . fake()->paragraphs(3, true) . '</p>';
        $status  = fake()->randomElement(StatusEnum::cases());

        return [
            'id'             => Str::uuid()->toString(),
            'title'          => fake()->sentence(4),
            'content'        => $content,
            'excerpt'        => Str::limit(strip_tags($content), 200),
            'owner_id'       => User::factory(),
            'status'         => $status,
            'visibility'     => fake()->randomElement(VisibilityEnum::cases()),
            'published_at'   => $status === StatusEnum::PUBLISHED ? fake()->dateTimeBetween('-1 year') : null,
            'date'           => fake()->optional(0.8)->dateTimeBetween('-2 years', 'now'),
            'location'       => fake()->city() . ', ' . fake()->country(),
            'latitude'       => fake()->optional(0.8)->latitude(),
            'longitude'      => fake()->optional(0.8)->longitude(),
            'likes_count'    => 0,
            'comments_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => StatusEnum::PUBLISHED,
            'visibility'   => VisibilityEnum::PUBLIC,
            'published_at' => fake()->dateTimeBetween('-1 year'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => StatusEnum::DRAFT,
            'published_at' => null,
        ]);
    }
}
