<?php

declare(strict_types=1);

namespace console\services;

use common\enums\UserStatusEnum;
use common\public_services\UserServiceInterface;
use console\models\forms\SignupForm;
use Yii;
use yii\helpers\VarDumper;

readonly class SignupService
{
    public function __construct(
        private UserServiceInterface $userService,
    ) {
    }

    public function signup(
        string $email,
        string $password
    ): bool {
        $signupForm = new SignupForm();

        $signupForm->setEmail($email);
        $signupForm->setPassword($password);

        if (!$signupForm->validate()) {
            Yii::error(
                sprintf(
                    'User creation command validation error. Error: %s',
                    VarDumper::dumpAsString($signupForm->getErrors())
                )
            );
            return false;
        }

        $dto = $signupForm->getDto();

        $result = $this->userService->create(
            $dto->getEmail(),
            $dto->getPassword(),
            UserStatusEnum::ACTIVE
        );

        return (bool)$result;
    }
}
