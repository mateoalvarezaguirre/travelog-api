<?php

declare(strict_types=1);

namespace Src\Social\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class CannotFollowSelfException extends BaseException
{
    public function __construct()
    {
        parent::__construct('social.cannot_follow_self', 422);
    }
}
