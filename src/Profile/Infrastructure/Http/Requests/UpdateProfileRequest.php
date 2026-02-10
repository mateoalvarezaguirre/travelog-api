<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Requests;

use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name'       => ['sometimes', 'string', 'max:255'],
            'bio'        => ['nullable', 'string', 'max:500'],
            'location'   => ['nullable', 'string', 'max:255'],
            'avatar'     => ['nullable', 'url', 'max:2048'],
            'coverPhoto' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
