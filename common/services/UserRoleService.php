<?php

declare(strict_types=1);

namespace common\services;

use common\public_services\UserRoleServiceInterface;

readonly class UserRoleService implements UserRoleServiceInterface
{
    public function __construct(
        private UserService $userService
    ) {
    }

    public function isGuest(): bool
    {
        return (null === $this->userService->getCurrentIdentity());
    }

    public function getCurrentUserId(): ?int
    {
        $userModel = $this->userService->getCurrentIdentity();
        return $userModel?->getDto()->getId();
    }
}
