<?php

declare(strict_types=1);

namespace common\dtos;

readonly class PasswordDataDto
{
    public function __construct(
        private string $authKey,
        private string $passwordHash,
        private ?string $passwordResetToken
    ) {
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
}
