<?php

declare(strict_types=1);

namespace common\services;

use common\public_services\UserAuthServiceInterface;
use common\repositories\UserIdentityRepository;
use Yii;
use yii\web\IdentityInterface;

readonly class UserAuthService implements UserAuthServiceInterface
{
    private const string PHP_SESSION_ID = 'PHPSESSID';

    public function __construct(
        private UserIdentityRepository $userIdentityRepository,
    ) {
    }

    public function authByEmail(string $email, int $rememberMeDurationInSec): bool
    {
        $identity = $this->userIdentityRepository->findIdentityByEmail($email);

        return $this->loginByIdentity(
            $identity,
            $rememberMeDurationInSec
        );
    }

    public function logout(): void
    {
        Yii::$app->user->logout();
        Yii::$app->response->cookies->remove(self::PHP_SESSION_ID);
    }

    private function loginByIdentity(?IdentityInterface $identity, int $duration): bool
    {
        if (null === $identity) {
            return false;
        }

        return Yii::$app->user->login($identity, $duration);
    }
}
