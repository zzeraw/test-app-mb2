<?php

declare(strict_types=1);

namespace common\public_interfaces;

use common\enums\UserStatusEnum;
use DateTimeImmutable;

interface UserDtoInterface
{
    public function getId(): int;

    public function getEmail(): string;

    public function getAuthKey(): string;

    public function getPasswordHash(): string;

    public function getPasswordResetToken(): ?string;

    public function getStatus(): UserStatusEnum;

    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): DateTimeImmutable;
}
