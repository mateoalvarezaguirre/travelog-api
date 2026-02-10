<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Domain\ValueObjects\Owner;
use Tests\TestCase;

/**
 * @internal
 */
class OwnerTest extends TestCase
{
    public function test_constructor_with_only_required_id(): void
    {
        $owner = new Owner(42);

        $this->assertSame(42, $owner->id);
        $this->assertNull($owner->getName());
        $this->assertNull($owner->getProfilePicture());
        $this->assertNull($owner->getPhrase());
        $this->assertNull($owner->getUsername());
    }

    public function test_constructor_with_all_parameters(): void
    {
        $owner = new Owner(
            id: 1,
            name: 'Alice',
            profilePicture: 'https://example.com/avatar.jpg',
            phrase: 'Wanderlust forever',
            username: 'alice_travels',
        );

        $this->assertSame(1, $owner->id);
        $this->assertSame('Alice', $owner->getName());
        $this->assertSame('https://example.com/avatar.jpg', $owner->getProfilePicture());
        $this->assertSame('Wanderlust forever', $owner->getPhrase());
        $this->assertSame('alice_travels', $owner->getUsername());
    }

    public function test_setters_update_values(): void
    {
        $owner = new Owner(1);

        $owner->setName('Bob');
        $owner->setProfilePicture('https://example.com/bob.jpg');
        $owner->setPhrase('Exploring the world');
        $owner->setUsername('bob_explorer');

        $this->assertSame('Bob', $owner->getName());
        $this->assertSame('https://example.com/bob.jpg', $owner->getProfilePicture());
        $this->assertSame('Exploring the world', $owner->getPhrase());
        $this->assertSame('bob_explorer', $owner->getUsername());
    }

    public function test_profile_picture_and_username_can_be_set_to_null(): void
    {
        $owner = new Owner(
            id: 1,
            name: 'Carol',
            profilePicture: 'https://example.com/carol.jpg',
            username: 'carol',
        );

        $owner->setProfilePicture(null);
        $owner->setUsername(null);

        $this->assertNull($owner->getProfilePicture());
        $this->assertNull($owner->getUsername());
    }

    public function test_id_is_readonly(): void
    {
        $owner = new Owner(99);

        $reflection = new \ReflectionProperty(Owner::class, 'id');

        $this->assertTrue($reflection->isReadOnly());
        $this->assertSame(99, $owner->id);
    }
}
