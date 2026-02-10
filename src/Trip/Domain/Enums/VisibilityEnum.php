<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Enums;

enum VisibilityEnum: string
{
    case PUBLIC   = 'public';
    case PRIVATE  = 'private';
    case UNLISTED = 'unlisted';
}
