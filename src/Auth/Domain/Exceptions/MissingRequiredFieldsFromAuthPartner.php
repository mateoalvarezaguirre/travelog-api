<?php

namespace Src\Auth\Domain\Exceptions;

use Src\Shared\Core\Domain\Exceptions\BaseException;

class MissingRequiredFieldsFromAuthPartner extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'auth_partner.missing_required_fields',
            422
        );
    }
}
