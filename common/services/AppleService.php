<?php

declare(strict_types=1);

namespace common\services;

use common\dtos\AppleStateItemDto;
use common\dtos\models\AppleDto;
use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use common\public_interfaces\AppleStateItemDtoInterface;
use common\public_services\AppleServiceInterface;
use common\repositories\AppleRepository;
use DateMalformedStringException;
use DateTimeImmutable;
use LogicException;
use Random\RandomException;
use Throwable;
use Yii;
use yii\base\Exception as BaseException;
use yii\db\Exception as DbException;

readonly class AppleService implements AppleServiceInterface
{
    private const int RANDOM_COUNT_START = 5;
    private const int RANDOM_COUNT_FINISH = 15;
    private const int RANDOM_HOURS_FROM = 10;
    private const int SPOIL_HOURS_TIMEOUT = 5;

    public function __construct(
        private AppleRepository $appleRepository,
    ) {
    }

    /**
     * @return AppleStateItemDtoInterface[]
     *
     * @throws BaseException
     * @throws DateMalformedStringException
     * @throws DbException
     * @throws RandomException
     * @throws Throwable
     */
    public function generate(int $userId): array
    {
        $randomCount = rand(self::RANDOM_COUNT_START, self::RANDOM_COUNT_FINISH);

        $transaction = Yii::$app->db->beginTransaction();

        $result = [];

        try {
            $this->appleRepository->setAsArchivedByUserId($userId);

            for ($i = 0; $i < $randomCount; $i++) {
                $result[] = $this->appleRepository->createNewModel(
                    $userId,
                    $this->getRandomColor(),
                    $this->getRandomDate(),
                );
            }

            $transaction->commit();
        } catch (Throwable $t) {
            $transaction->rollBack();

            throw $t;
        }

        return array_map(function (AppleDto $dto) {
            return $this->convertAppleDtoToAppleStateItemDto($dto);
        }, $result);
    }

    /**
     * @return AppleStateItemDtoInterface[]
     */
    public function findActiveStateDtosByUserId(int $userId): array
    {
        $dtos = $this->appleRepository->findActiveDtosByUserId($userId);

        return array_map(function (AppleDto $dto) {
            return $this->convertAppleDtoToAppleStateItemDto($dto);
        }, $dtos);
    }

    public function findActiveStateDtoByUserIdAndId($userId, $appleId): ?AppleStateItemDtoInterface
    {
        $appleDto = $this->appleRepository->findActiveDtoByUserIdAndId($userId, $appleId);

        if (null === $appleDto) {
            return null;
        }

        return $this->convertAppleDtoToAppleStateItemDto($appleDto);
    }

    public function fallDown($userId, $appleId): void
    {
        $dto = $this->appleRepository->findActiveDtoByUserIdAndId($userId, $appleId);

        if (null === $dto) {
            throw new LogicException(sprintf('Яблоко #%d не найдено!', $appleId));
        }

        if (AppleStatusEnum::ON_GROUND === $dto->getStatus()) {
            throw new LogicException(
                sprintf('Яблоко #%d нельзя уронить, оно уже лежит на земле!', $appleId)
            );
        }

        $this->appleRepository->fallDownModel($userId, $appleId);
    }

    public function eat(int $userId, int $appleId, int $biteSizePercent): void
    {
        $dto = $this->appleRepository->findActiveDtoByUserIdAndId($userId, $appleId);

        if (null === $dto) {
            throw new LogicException(sprintf('Яблоко #%d не найдено!', $appleId));
        }

        if (AppleStatusEnum::ON_GROUND !== $dto->getStatus()) {
            throw new LogicException(
                sprintf('Яблоко #%d нельзя откусить, оно еще висит на дереве!', $appleId)
            );
        }

        if ($biteSizePercent <= 0 || $biteSizePercent > 100) {
            throw new LogicException('Размер укуса должен быть в диапазоне 1-100%.');
        }

        $appleStateItemDto = $this->convertAppleDtoToAppleStateItemDto($dto);

        if (true === $appleStateItemDto->isSpoil()) {
            throw new LogicException(
                sprintf('Яблоко #%d нельзя откусить - оно испортилось!', $appleId)
            );
        }

        if ($dto->getSizePercent() <= 0) {
            throw new LogicException(
                sprintf('Яблоко #%d уже съедено!', $appleId)
            );
        }

        if ($biteSizePercent > $dto->getSizePercent()) {
            throw new LogicException(
                sprintf(
                    'Нельзя откусить %d%% от яблока #%d - осталось только %d%%.',
                    $biteSizePercent,
                    $appleId,
                    $dto->getSizePercent()
                )
            );
        }

        $newSizePercent = $dto->getSizePercent() - $biteSizePercent;
        if ($newSizePercent < 0) {
            $newSizePercent = 0;
        }

        $this->appleRepository->eatModel($userId, $appleId, $newSizePercent);
    }

    /**
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    private function getRandomDate(): DateTimeImmutable
    {
        $now = new DateTimeImmutable('now');
        $from = $now->modify(sprintf('-%d hours', self::RANDOM_HOURS_FROM));

        $randomTimestamp = random_int(
            $from->getTimestamp(),
            $now->getTimestamp()
        );

        return new DateTimeImmutable()->setTimestamp($randomTimestamp);
    }

    private function getRandomColor(): AppleColorEnum
    {
        $cases = AppleColorEnum::cases();

        return $cases[array_rand($cases)];
    }

    private function convertAppleDtoToAppleStateItemDto(AppleDto $appleDto): AppleStateItemDtoInterface
    {
        $fellAt = $appleDto->getFellAt();

        $isOnTree = (null === $fellAt);
        $isOnGround = !$isOnTree;

        $isSpoil = false;
        if (true === $isOnGround) {
            $nowTs = (new DateTimeImmutable('now'))->getTimestamp();
            $fellTs = $fellAt->getTimestamp();

            $isSpoil = ($nowTs - $fellTs) >= self::SPOIL_HOURS_TIMEOUT * 3600;
        }

        $canFall = $isOnTree;

        $canEat = $isOnGround
            && !$isSpoil
            && $appleDto->getSizePercent() > 0;

        return new AppleStateItemDto(
            $appleDto->getId(),
            $appleDto->getColor(),
            $appleDto->getSizePercent(),
            $appleDto->getStatus(),
            $canFall,
            $canEat,
            $isSpoil,
            $appleDto->getAppearedAt(),
            $fellAt
        );
    }
}
