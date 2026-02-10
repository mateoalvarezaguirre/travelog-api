<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Database\Repositories;

use App\Models\TripMedia;
use Src\Trip\Domain\Repositories\TripMediaRepository;
use Src\Trip\Domain\ValueObjects\RegisteredMedia;

class TripMediaEloquentRepository implements TripMediaRepository
{
    public function register(int $uploadedBy, string $url, ?string $caption): RegisteredMedia
    {
        $media = TripMedia::create([
            'media_type'  => 'image',
            'media_url'   => $url,
            'caption'     => $caption,
            'order'       => 0,
            'is_featured' => false,
            'is_visible'  => true,
            'uploaded_by' => $uploadedBy,
        ]);

        return new RegisteredMedia($media->id, $media->media_url);
    }
}
