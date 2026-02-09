<?php

namespace Src\Trip\Domain\Enums;

enum StatusEnum: string
{
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED  = 'archived';
}
