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
    private const int SITE_PERCENT_DEFAULT_VALUE = 100;

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

    public function findActiveDtoByUserIdAndId(int $userId, int $id): ?AppleDto
    {
        return $this->findActiveModelByUserIdAndId($userId, $id)?->getDto();
    }

    /**
     * @throws DbException
     * @throws BaseException
     */
    public function createNewModel(
        int $userId,
        AppleColorEnum $color,
        DateTimeImmutable $appearedAt
    ): AppleDto {
        $appleModel = new Apple();

        $appleModel->setUserId($userId);
        $appleModel->setColor($color->value);
        $appleModel->setSizePercent(self::SITE_PERCENT_DEFAULT_VALUE);
        $appleModel->setStatus(AppleStatusEnum::ON_TREE->value);
        $appleModel->setAppearedAt($appearedAt->format(self::DATETIME_FORMAT));
        $appleModel->setFellAt(null);
        $appleModel->setCreatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));
        $appleModel->setUpdatedAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));
        $appleModel->setIsArchive(false);

        $saveResult = $appleModel->save();

        if (false === $saveResult) {
            throw new BaseException(
                sprintf(
                    'Cannot save apple data. Error: %s',
                    VarDumper::dumpAsString($appleModel->getErrors())
                )
            );
        }

        return $appleModel->getDto();
    }

    /**
     * @throws BaseException
     * @throws DbException
     */
    public function fallDownModel(
        int $userId,
        int $id,
    ): bool {
        $appleModel = $this->findModelByUserIdAndId($userId, $id);

        if (null === $appleModel) {
            throw new BaseException(
                sprintf('Apple %d not found for user %d.', $id, $userId)
            );
        }

        $appleModel->setUserId($userId);
        $appleModel->setStatus(AppleStatusEnum::ON_GROUND->value);
        $appleModel->setFellAt((new DateTimeImmutable())->format(self::DATETIME_FORMAT));
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

        return true;
    }

    /**
     * @throws BaseException
     * @throws DbException
     */
    public function eatModel(
        int $userId,
        int $id,
        int $sizePercent,
    ): bool {
        $appleModel = $this->findModelByUserIdAndId($userId, $id);

        if (null === $appleModel) {
            throw new BaseException(
                sprintf('Apple %d not found for user %d.', $id, $userId)
            );
        }

        $appleModel->setUserId($userId);
        $appleModel->setSizePercent($sizePercent);
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

        return true;
    }

    public function setAsArchivedByUserId(
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

    private function findActiveModelByUserIdAndId(int $userId, int $appleId): ?Apple
    {
        $model = Apple::find()
            ->where([
                'user_id' => $userId,
                'id' => $appleId,
                'is_archive' => (int)false,
            ])
            ->one();

        if (null === $model) {
            return null;
        }

        assert($model instanceof Apple);

        return $model;
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
