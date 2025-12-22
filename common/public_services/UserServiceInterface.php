<?php

declare(strict_types=1);

namespace common\public_services;

use common\enums\UserStatusEnum;

interface UserServiceInterface
{
    public function create(string $email, string $password, UserStatusEnum $status): int;
}
