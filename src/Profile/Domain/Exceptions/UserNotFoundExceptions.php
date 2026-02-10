<?php

declare(strict_types=1);

namespace Src\Profile\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class UserNotFoundExceptions extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'profile.user_not_found',
            404
        );
    }
}
