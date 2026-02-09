<?php

namespace Src\Auth\Infrastructure\Http\Requests;

use Src\Auth\Application\DTO\In\LoginDTO;
use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function dto(): LoginDTO
    {
        return new LoginDTO(
            email: $this->input('email'),
            password: $this->input('password'),
        );
    }
}

