<?php

declare(strict_types=1);

namespace backend\enums;

enum ResponseStatusEnum: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
}
