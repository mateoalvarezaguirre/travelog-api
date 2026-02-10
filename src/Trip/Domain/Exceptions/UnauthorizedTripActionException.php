<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class UnauthorizedTripActionException extends BaseException
{
    public function __construct()
    {
        parent::__construct('trip.unauthorized', 403);
    }
}
