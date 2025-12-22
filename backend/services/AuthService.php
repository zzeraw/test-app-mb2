<?php

declare(strict_types=1);

namespace backend\services;

use backend\models\forms\LoginForm;
use common\enums\TranslationCategoryEnum;
use common\enums\UserStatusEnum;
use common\public_services\PasswordServiceInterface;
use common\public_services\UserAuthServiceInterface;
use common\public_services\UserServiceInterface;
use Yii;

readonly class AuthService
{
    private const string EMAIL_LOGIN_FORM_ATTRIBUTE = 'email';
    private const string PASSWORD_LOGIN_FORM_ATTRIBUTE = 'password';
    private const string PHP_SESSION_ID = 'PHPSESSID';
    private const int REMEMBER_ME_DURATION = 3600 * 24 * 30;

    public function __construct(
        private UserAuthServiceInterface $userAuthService,
        private UserServiceInterface $userService,
        private PasswordServiceInterface $passwordService
    ) {
    }

    public function login(LoginForm $loginForm, array $postData): bool
    {
        if ($loginForm->load($postData)) {
            if (!$loginForm->validate()) {
                return false;
            }

            if (!$this->validateFormPassword($loginForm)) {
                return false;
            }

            $dto = $loginForm->getDto();

            return $this->userAuthService->authByEmail(
                $dto->getEmail(),
                $dto->isRememberMe() ? self::REMEMBER_ME_DURATION : 0,
            );
        }

        return false;
    }

    public function logout(): void
    {
        Yii::$app->user->logout();
        Yii::$app->response->cookies->remove(self::PHP_SESSION_ID);
    }

    private function validateFormPassword(LoginForm $loginForm): bool
    {
        if ($loginForm->hasErrors()) {
            return false;
        }

        $loginDto = $loginForm->getDto();
        $userDto = $this->userService->getDtoByEmail($loginDto->getEmail());

        if (null === $userDto) {
            $loginForm->addError(
                self::EMAIL_LOGIN_FORM_ATTRIBUTE,
                Yii::t(TranslationCategoryEnum::NAME->value, 'This user is not found')
            );
            return false;
        }

        $validationPasswordResult = $this->passwordService->validatePasswordByHash(
            $loginDto->getPassword(),
            $userDto->getPasswordHash()
        );

        if (false === $validationPasswordResult) {
            $loginForm->addError(
                self::PASSWORD_LOGIN_FORM_ATTRIBUTE,
                Yii::t(TranslationCategoryEnum::NAME->value, 'Wrong password')
            );
            return false;
        }

        if (UserStatusEnum::BLOCKED === $userDto->getStatus()) {
            $loginForm->addError(
                self::EMAIL_LOGIN_FORM_ATTRIBUTE,
                Yii::t(TranslationCategoryEnum::NAME->value, 'This user is blocked')
            );
            return false;
        }

        return true;
    }
}
