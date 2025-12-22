<?php

declare(strict_types=1);

namespace common\public_services;

interface PasswordServiceInterface
{
    public function validatePasswordByHash(string $password, string $hash): bool;
}
