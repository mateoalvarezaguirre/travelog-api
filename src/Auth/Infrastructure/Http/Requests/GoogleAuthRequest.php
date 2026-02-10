<?php

namespace Src\Auth\Infrastructure\Http\Requests;

use Src\Auth\Application\DTO\In\GoogleAuthDTO;
use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class GoogleAuthRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
        ];
    }

    public function dto(): GoogleAuthDTO
    {
        return new GoogleAuthDTO(
            googleId: $this->input('id_token'),
        );
    }
}
