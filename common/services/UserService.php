<?php

declare(strict_types=1);

namespace common\services;

use common\enums\UserStatusEnum;
use common\models\User;
use common\public_interfaces\UserDtoInterface;
use common\public_services\UserServiceInterface;
use common\repositories\UserRepository;
use Yii;
use yii\base\Exception;

readonly class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordService $passwordService
    ) {
    }

    /**
     * @throws Exception
     */
    public function create(string $email, string $password, UserStatusEnum $status): int
    {
        $passwordDataDto = $this->passwordService->createPasswordDataDto($password);

        $userId = $this->userRepository->createUser(
            $email,
            $passwordDataDto,
            $status
        );

        return $userId;
    }

    public function getCurrentIdentity(): ?User
    {
        return $this->currentModel();
    }

    public function getDtoByEmail(string $email): ?UserDtoInterface
    {
        return $this->userRepository->findDtoByEmail(trim($email));
    }

    private function currentModel(): ?User
    {
        if (isset(Yii::$app->user)) {
            /* @var $userModel User */
            $userModel = Yii::$app->getUser()->getIdentity();
            if (null !== $userModel) {
                assert($userModel instanceof User);
                return $userModel;
            }
        }
        return null;
    }
}
