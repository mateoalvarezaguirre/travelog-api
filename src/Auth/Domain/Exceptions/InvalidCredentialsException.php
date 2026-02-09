<?php

namespace Src\Auth\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class InvalidCredentialsException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'auth.invalid_credentials',
            401
        );
    }
}
