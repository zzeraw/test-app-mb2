<?php

declare(strict_types=1);

namespace backend\dtos;

readonly class LoginFormDto
{
    public function __construct(
        private string $email,
        private string $password,
        private bool $rememberMe
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }
}
