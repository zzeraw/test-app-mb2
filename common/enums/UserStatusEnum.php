<?php

declare(strict_types=1);

namespace common\enums;

enum UserStatusEnum: int
{
    case BLOCKED = 0;
    case ACTIVE = 1;
}
