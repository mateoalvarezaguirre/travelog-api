<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Requests;

use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;
use Src\Trip\Application\DTOs\In\CreateTripDTO;

class StoreTripRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'content'         => ['required', 'string'],
            'date'            => ['nullable', 'date'],
            'location'        => ['nullable', 'string', 'max:255'],
            'coordinates'     => ['nullable', 'array'],
            'coordinates.lat' => ['required_with:coordinates', 'numeric', 'between:-90,90'],
            'coordinates.lng' => ['required_with:coordinates', 'numeric', 'between:-180,180'],
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['string', 'max:50'],
            'status'          => ['nullable', 'string', 'in:draft,published'],
            'isPublic'        => ['nullable', 'boolean'],
            'imageIds'        => ['nullable', 'array'],
            'imageIds.*'      => ['integer', 'exists:trip_media,id'],
        ];
    }

    public function dto(): CreateTripDTO
    {
        return new CreateTripDTO(
            title: $this->input('title'),
            content: $this->input('content'),
            ownerId: $this->user()->id,
            date: $this->input('date'),
            location: $this->input('location'),
            latitude: $this->input('coordinates.lat'),
            longitude: $this->input('coordinates.lng'),
            tags: $this->input('tags', []),
            status: $this->input('status', 'draft'),
            isPublic: $this->boolean('isPublic', true),
            imageIds: $this->input('imageIds', []),
        );
    }
}
