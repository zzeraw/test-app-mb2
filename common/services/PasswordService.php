<?php

declare(strict_types=1);

namespace common\services;

use common\dtos\PasswordDataDto;
use common\public_services\PasswordServiceInterface;
use Yii;

class PasswordService implements PasswordServiceInterface
{
    public function validatePasswordByHash(string $password, string $hash): bool
    {
        $salt = $this->getSalt();

        return Yii::$app->getSecurity()->validatePassword(
            sprintf('%s%s', $password, $salt),
            $hash
        );
    }

    public function createPasswordDataDto(?string $rawPassword): PasswordDataDto
    {
        if (null === $rawPassword) {
            $rawPassword = $this->generateRandomString();
        }

        $salt = $this->getSalt();

        $passwordHash = Yii::$app->getSecurity()->generatePasswordHash(
            sprintf('%s%s', $rawPassword, $salt)
        );

        $passwordResetToken = null;
        $authKey = $this->generateRandomString();

        return new PasswordDataDto(
            $authKey,
            $passwordHash,
            $passwordResetToken
        );
    }

    private function generateRandomString(): string
    {
        return Yii::$app->getSecurity()->generateRandomString();
    }

    private function getSalt(): string
    {
        return (string)env('USER_SALT');
    }
}
