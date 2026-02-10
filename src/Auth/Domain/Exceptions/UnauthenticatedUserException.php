<?php

declare(strict_types=1);

namespace Src\Auth\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class UnauthenticatedUserException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'auth.unauthenticated_user',
            401
        );
    }
}
