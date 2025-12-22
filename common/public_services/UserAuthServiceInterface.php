<?php

declare(strict_types=1);

namespace common\public_services;

interface UserAuthServiceInterface
{
    public function authByEmail(string $email, int $rememberMeDurationInSec): bool;
}
