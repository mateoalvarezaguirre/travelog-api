<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Http\Requests;

use Src\Place\Application\DTOs\CreatePlaceDTO;
use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class StorePlaceRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'country'         => ['required', 'string', 'max:255'],
            'date'            => ['nullable', 'date'],
            'coordinates'     => ['required', 'array'],
            'coordinates.lat' => ['required', 'numeric', 'between:-90,90'],
            'coordinates.lng' => ['required', 'numeric', 'between:-180,180'],
            'marker_type'     => ['required', 'string', 'in:default,visited,wishlist'],
            'image'           => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function dto(): CreatePlaceDTO
    {
        return new CreatePlaceDTO(
            userId: $this->user()->id,
            name: $this->input('name'),
            country: $this->input('country'),
            date: $this->input('date'),
            latitude: (float) $this->input('coordinates.lat'),
            longitude: (float) $this->input('coordinates.lng'),
            markerType: $this->input('marker_type') ?? '',
            image: $this->input('image'),
        );
    }
}
