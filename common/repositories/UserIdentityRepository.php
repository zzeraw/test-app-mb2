<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\User;
use yii\web\IdentityInterface;

readonly class UserIdentityRepository
{
    public function findIdentityByEmail(string $email): ?IdentityInterface
    {
        $userModel = User::findOne([
            'email' => $email,
        ]);

        if (null === $userModel) {
            return null;
        }

        if (!($userModel instanceof IdentityInterface)) {
            return null;
        }

        return $userModel;
    }
}
