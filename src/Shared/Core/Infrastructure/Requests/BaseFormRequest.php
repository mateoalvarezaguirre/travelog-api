<?php

declare(strict_types=1);

namespace Src\Shared\Core\Infrastructure\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Src\Shared\Core\Domain\Exceptions\BadRequestException;

class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @throws BadRequestException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new BadRequestException(
            $validator->errors()->all()
        );
    }
}
