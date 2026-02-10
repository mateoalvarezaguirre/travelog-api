<?php

namespace Src\Shared\Core\Domain\Exceptions;

class DatabaseException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'database.internal_error',
            500
        );
    }
}
