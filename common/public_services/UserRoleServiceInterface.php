<?php

declare(strict_types=1);

namespace common\public_services;

interface UserRoleServiceInterface
{
    public function isGuest(): bool;

    public function getCurrentUserId(): ?int;
}
