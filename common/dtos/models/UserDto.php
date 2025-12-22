<?php

declare(strict_types=1);

namespace common\dtos\models;

use common\enums\UserStatusEnum;
use common\public_interfaces\UserDtoInterface;
use DateTimeImmutable;

readonly class UserDto implements UserDtoInterface
{
    public function __construct(
        private int $id,
        private string $email,
        private string $authKey,
        private string $passwordHash,
        private ?string $passwordResetToken,
        private UserStatusEnum $status,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
