<?php

declare(strict_types=1);

namespace Src\Place\Domain\Enums;

enum MarkerTypeEnum: string
{
    case VISITED  = 'visited';
    case PLANNED  = 'planned';
    case WISHLIST = 'wishlist';
}
