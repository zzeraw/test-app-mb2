<?php

declare(strict_types=1);

namespace common\repositories;

use common\dtos\models\AppleDto;
use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use common\models\Apple;
use DateTimeImmutable;
use yii\base\Exception as BaseException;
use yii\db\Exception as DbException;
use yii\helpers\VarDumper;


readonly class AppleRepository
{
    private const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @return AppleDto[]
     */
    public function findActiveDtosByUserId(int $userId): array
    {
        $models = $this->findActiveModelsByUserId($userId);

        return array_map(function (Apple $model) {
            return $model->getDto();
        }, $models);
    }

    public function findDtoByUserIdAndId(int $userId, int $id): ?AppleDto
    {
        return $this->findModelByUserIdAndId($userId, $id)?->getDto();
    }

    /**
     * @throws DbException
     * @throws BaseException
     */
    private function createNewModel(
        int $userId,
        AppleColorEnum $color,
        DateTimeImmutable $appearedAt
    ): int {
        $appleModel = new Apple();

        $appleModel->setUserId($userId);
        $appleModel->setColor($color->value);
        $appleModel->setStatus(AppleStatusEnum::ON_TREE->value);
        $appleModel->setAppearedAt($appearedAt->format(self::DATETIME_FORMAT));
        $appleModel->setFellAt(null);
        $appleModel->setCreatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));
        $appleModel->setUpdatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));

        $saveResult = $appleModel->save();

        if (false === $saveResult) {
            throw new BaseException(
                sprintf(
                    'Cannot save apple data. Error: %s',
                    VarDumper::dumpAsString($appleModel->getErrors())
                )
            );
        }

        return $appleModel->getPrimaryKey();
    }

    /**
     * @throws BaseException
     * @throws DbException
     */
    private function updateModel(
        int $id,
        int $userId,
        AppleColorEnum $color,
        float $sizePercent,
        AppleStatusEnum $status,
        ?DateTimeImmutable $fellAt,
    ): int {
        $appleModel = $this->findModelByUserIdAndId($userId, $id);

        if (null === $appleModel) {
            throw new BaseException(
                sprintf('Apple %d not found for user %d.', $id, $userId)
            );
        }

        $appleModel->setUserId($userId);
        $appleModel->setColor($color->value);
        $appleModel->setSizePercent($sizePercent);
        $appleModel->setStatus($status->value);
        $appleModel->setFellAt($fellAt?->format(self::DATETIME_FORMAT));
        $appleModel->setUpdatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));

        $saveResult = $appleModel->save();

        if (false === $saveResult) {
            throw new BaseException(
                sprintf(
                    'Cannot save apple data. Error: %s',
                    VarDumper::dumpAsString($appleModel->getErrors())
                )
            );
        }

        return $appleModel->getPrimaryKey();
    }

    private function setAsArchivedByUserId(
        int $userId,
    ): int {
        return Apple::updateAll(
            [
                'is_archive' => (int)true,
            ],
            [
                'user_id' => $userId,
            ]
        );
    }

    private function findModelByUserIdAndId(int $userId, int $appleId): ?Apple
    {
        $model = Apple::find()
            ->where([
                'user_id' => $userId,
                'id' => $appleId,
            ])
            ->one();

        if (null === $model) {
            return null;
        }

        assert($model instanceof Apple);

        return $model;
    }

    /**
     * @return Apple[]
     */
    private function findActiveModelsByUserId(int $userId): array
    {
        return Apple::find()
            ->where([
                'user_id' => $userId,
                'is_archive' => (int)false,
            ])
            ->orderBy('id')
            ->all();
    }
}
