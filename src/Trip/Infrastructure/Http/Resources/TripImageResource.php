<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Resources;

use App\Models\TripMedia;

readonly class TripImageResource implements \JsonSerializable
{
    public function __construct(
        private TripMedia $media,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'      => $this->media->id,
            'url'     => $this->media->media_url,
            'caption' => $this->media->caption,
            'order'   => $this->media->order,
        ];
    }

    /**
     * @param iterable<TripMedia> $mediaItems
     *
     * @return array<int, array<string, mixed>>
     */
    public static function collection(iterable $mediaItems): array
    {
        $result = [];
        foreach ($mediaItems as $media) {
            $result[] = (new self($media))->jsonSerialize();
        }

        return $result;
    }
}
