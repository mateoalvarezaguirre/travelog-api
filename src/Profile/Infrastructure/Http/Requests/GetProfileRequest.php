<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Requests;

use Src\Profile\Application\DTO\In\GetProfileDTO;
use Src\Shared\Core\Infrastructure\Requests\BaseFormRequest;

class GetProfileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()->id;
    }

    public function dto(): GetProfileDTO
    {
        return new GetProfileDTO(
            email: $this->user()?->email() ?? '',
        );
    }
}
