<?php

declare(strict_types=1);

namespace Src\Shared\Core\Domain\Exceptions;

class InvalidEmailException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'shared.invalid_email',
            409
        );
    }
}
