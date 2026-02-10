<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Carbon\Carbon;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;
use Tests\TestCase;

/**
 * @internal
 */
class TripEntityTest extends TestCase
{
    public function test_constructor_sets_readonly_properties(): void
    {
        $owner       = new Owner(1, 'John');
        $engagement  = new Engagement(5, 3, 1, 100);
        $createdAt   = Carbon::parse('2025-06-01');
        $publishedAt = Carbon::parse('2025-06-02');

        $entity = new TripEntity(
            title: 'Road Trip',
            content: 'We drove across the country',
            owner: $owner,
            status: StatusEnum::PUBLISHED,
            visibility: VisibilityEnum::PUBLIC,
            engagement: $engagement,
            createdAt: $createdAt,
            publishedAt: $publishedAt,
            privateContent: 'Private diary entry',
        );

        $this->assertSame('Road Trip', $entity->title);
        $this->assertSame('We drove across the country', $entity->content);
        $this->assertSame($owner, $entity->owner);
        $this->assertSame(StatusEnum::PUBLISHED, $entity->status);
        $this->assertSame(VisibilityEnum::PUBLIC, $entity->visibility);
        $this->assertSame($engagement, $entity->engagement);
        $this->assertSame($createdAt, $entity->createdAt);
        $this->assertSame($publishedAt, $entity->publishedAt);
        $this->assertSame('Private diary entry', $entity->privateContent);
    }

    public function test_set_and_get_uuid(): void
    {
        $entity = $this->create_entity();

        $entity->setUuid('abc-123-def');

        $this->assertSame('abc-123-def', $entity->getUuid());

        $entity->setId('xyz-789');

        $this->assertSame('xyz-789', $entity->getUuid());
    }

    public function test_nullable_setters_and_getters_default_to_null(): void
    {
        $entity = $this->create_entity();

        $this->assertNull($entity->getExcerpt());
        $this->assertNull($entity->getDate());
        $this->assertNull($entity->getLocation());
        $this->assertNull($entity->getLatitude());
        $this->assertNull($entity->getLongitude());
        $this->assertNull($entity->getUpdatedAt());

        $entity->setExcerpt('A short summary');
        $entity->setDate(Carbon::parse('2025-03-15'));
        $entity->setLocation('Paris, France');
        $entity->setLatitude(48.8566);
        $entity->setLongitude(2.3522);
        $updatedAt = Carbon::now();
        $entity->setUpdatedAt($updatedAt);

        $this->assertSame('A short summary', $entity->getExcerpt());
        $this->assertSame('2025-03-15', $entity->getDate()?->toDateString());
        $this->assertSame('Paris, France', $entity->getLocation());
        $this->assertSame(48.8566, $entity->getLatitude());
        $this->assertSame(2.3522, $entity->getLongitude());
        $this->assertSame($updatedAt, $entity->getUpdatedAt());
    }

    public function test_is_public_returns_true_only_for_public_visibility(): void
    {
        $publicEntity   = $this->create_entity(visibility: VisibilityEnum::PUBLIC);
        $privateEntity  = $this->create_entity(visibility: VisibilityEnum::PRIVATE);
        $unlistedEntity = $this->create_entity(visibility: VisibilityEnum::UNLISTED);

        $this->assertTrue($publicEntity->isPublic());
        $this->assertFalse($privateEntity->isPublic());
        $this->assertFalse($unlistedEntity->isPublic());
    }

    public function test_tags_images_and_is_liked_setters(): void
    {
        $entity = $this->create_entity();

        $this->assertSame([], $entity->getTags());
        $this->assertSame([], $entity->getImageIds());
        $this->assertSame([], $entity->getImages());
        $this->assertFalse($entity->isLiked());

        $entity->setTags(['travel', 'europe', 'adventure']);
        $entity->setImageIds([1, 2, 3]);
        $entity->setImages([
            ['id' => 1, 'url' => 'https://example.com/1.jpg'],
            ['id' => 2, 'url' => 'https://example.com/2.jpg'],
        ]);
        $entity->setIsLiked(true);

        $this->assertSame(['travel', 'europe', 'adventure'], $entity->getTags());
        $this->assertSame([1, 2, 3], $entity->getImageIds());
        $this->assertCount(2, $entity->getImages());
        $this->assertSame('https://example.com/1.jpg', $entity->getImages()[0]['url']);
        $this->assertTrue($entity->isLiked());
    }

    private function create_entity(
        VisibilityEnum $visibility = VisibilityEnum::PUBLIC,
        StatusEnum $status = StatusEnum::DRAFT,
    ): TripEntity {
        return new TripEntity(
            title: 'My Trip',
            content: '<p>Great adventure</p>',
            owner: new Owner(1, 'John'),
            status: $status,
            visibility: $visibility,
            engagement: new Engagement(),
            createdAt: Carbon::parse('2025-01-15 10:00:00'),
            publishedAt: Carbon::parse('2025-01-16 12:00:00'),
            privateContent: 'Secret notes',
        );
    }
}
