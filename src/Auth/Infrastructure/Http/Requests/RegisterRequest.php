<?php

namespace Src\Auth\Infrastructure\Http\Requests;

use Src\Auth\Application\DTO\In\RegisterDTO;
use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class RegisterRequest extends BaseFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        ];
    }

    public function dto(): RegisterDTO
    {
        return new RegisterDTO(
            name: $this->input('name'),
            email: $this->input('email'),
            password: $this->input('password'),
            username: $this->input('username'),
        );
    }
}
