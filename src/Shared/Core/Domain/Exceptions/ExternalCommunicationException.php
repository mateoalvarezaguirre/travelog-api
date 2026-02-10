<?php

declare(strict_types=1);

namespace Src\Shared\Core\Domain\Exceptions;

class ExternalCommunicationException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'external_service_communication_error',
            500
        );
    }
}
