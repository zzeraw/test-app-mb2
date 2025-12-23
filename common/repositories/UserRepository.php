<?php

declare(strict_types=1);

namespace common\repositories;

use common\dtos\PasswordDataDto;
use common\enums\UserStatusEnum;
use common\models\User;
use common\public_interfaces\UserDtoInterface;
use DateTimeImmutable;
use yii\base\Exception;
use yii\helpers\VarDumper;

readonly class UserRepository
{
    private const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function findDtoByEmail(string $email): ?UserDtoInterface
    {
        $model = $this->findModelByEmail($email);

        if (null === $model) {
            return null;
        }

        assert($model instanceof User);

        return $model->getDto();
    }

    /**
     * @throws Exception
     */
    public function createUser(
        string $email,
        PasswordDataDto $passwordDataDto,
        UserStatusEnum $status
    ): int {
        return $this->createNewModel(
            $email,
            $passwordDataDto->getAuthKey(),
            $passwordDataDto->getPasswordHash(),
            $passwordDataDto->getPasswordResetToken(),
            $status,
        );
    }

    /**
     * @throws Exception
     */
    private function createNewModel(
        string $email,
        string $authKey,
        string $passwordHash,
        ?string $passwordResetToken,
        UserStatusEnum $status
    ): int {
        $userModel = new User();

        $userModel->setEmail($email);
        $userModel->setAuthKey($authKey);
        $userModel->setPasswordHash($passwordHash);
        $userModel->setPasswordResetToken($passwordResetToken);
        $userModel->setStatus($status->value);
        $userModel->setCreatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));
        $userModel->setUpdatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));

        $saveResult = $userModel->save();

        if (false === $saveResult) {
            throw new Exception(
                sprintf(
                    'Cannot save user data. Error: %s',
                    VarDumper::dumpAsString($userModel->getErrors())
                )
            );
        }

        return $userModel->getPrimaryKey();
    }

    private function findModelByEmail(string $email): ?User
    {
        $model = User::find()
            ->where([
                'email' => $email,
            ])
            ->one();

        if (null === $model) {
            return null;
        }

        assert($model instanceof User);

        return $model;
    }
}
