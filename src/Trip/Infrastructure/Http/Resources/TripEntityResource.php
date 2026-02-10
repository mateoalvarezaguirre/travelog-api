<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Resources;

use Src\Trip\Domain\Entities\TripEntity;

readonly class TripEntityResource implements \JsonSerializable
{
    public function __construct(
        private TripEntity $trip,
        private bool $isLiked = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $owner       = $this->trip->owner;
        $coordinates = ($this->trip->getLatitude() !== null && $this->trip->getLongitude() !== null)
            ? ['lat' => $this->trip->getLatitude(), 'lng' => $this->trip->getLongitude()]
            : null;

        $author = null;
        if ($owner->getName() !== null || $owner->getUsername() !== null) {
            $author = [
                'id'       => $owner->id,
                'name'     => $owner->getName(),
                'username' => $owner->getUsername(),
                'avatar'   => $owner->getProfilePicture(),
            ];
        }

        return [
            'id'            => $this->trip->getUuid(),
            'title'         => $this->trip->title,
            'content'       => $this->trip->content,
            'excerpt'       => $this->trip->getExcerpt(),
            'date'          => $this->trip->getDate(),
            'location'      => $this->trip->getLocation(),
            'coordinates'   => $coordinates,
            'images'        => $this->trip->getImages(),
            'tags'          => $this->trip->getTags(),
            'status'        => $this->trip->status->value,
            'isPublic'      => $this->trip->visibility->value === 'public',
            'likesCount'    => $this->trip->engagement->getLikesCount(),
            'commentsCount' => $this->trip->engagement->getCommentsCount(),
            'isLiked'       => $this->isLiked,
            'author'        => $author,
            'createdAt'     => $this->trip->createdAt->toIso8601String(),
            'updatedAt'     => $this->trip->getUpdatedAt()?->toIso8601String(),
        ];
    }
}
