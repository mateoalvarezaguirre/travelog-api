<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Requests;

use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class RegisterMediaRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'url'     => ['required', 'url', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
