<?php

declare(strict_types=1);

namespace Src\Shared\Core\Domain\Exceptions;

class BadRequestException extends BaseException
{
    public function __construct(array $errors = [])
    {
        parent::__construct(
            'request.wrong_fields',
            400,
            $errors
        );
    }
}
