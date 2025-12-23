<?php

declare(strict_types=1);

namespace backend\services;

use backend\dtos\response\AppleItemResponseDto;
use backend\dtos\response\GenerateApplesResponseDto;
use backend\enums\ResponseStatusEnum;
use common\enums\AppleStatusEnum;
use common\public_interfaces\AppleStateItemDtoInterface;
use common\public_services\AppleServiceInterface;
use common\public_services\UserRoleServiceInterface;
use DateTimeImmutable;
use LogicException;
use Throwable;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

readonly class AppleManageService
{
    public function __construct(
        private AppleServiceInterface $appleService,
        private UserRoleServiceInterface $userRoleService,
    ) {
    }

    public function generate(int $userId): GenerateApplesResponseDto
    {
        $appleStateItemDtos = [];

        try {
            $currentUserId = $this->userRoleService->getCurrentUserId();
            if ($currentUserId !== $userId) {
                throw new ForbiddenHttpException(
                    'Ошибка прав доступа. Авторизированный пользователь не совпадает с пользователем в запросе.'
                );
            }

            $appleStateItemDtos = $this->appleService->generate($userId);
        } catch (Throwable $t) {
            Yii::error(
                sprintf(
                    'AppleManageService::generate error for user %d: %s. Trace: %s',
                    $userId,
                    $t->getMessage(),
                    $t->getTraceAsString(),
                )
            );

            $errorMessage = $this->generateErrorMessage($t);

            return new GenerateApplesResponseDto(
                ResponseStatusEnum::FAIL,
                $errorMessage,
                $appleStateItemDtos
            );
        }

        return new GenerateApplesResponseDto(
            ResponseStatusEnum::SUCCESS,
            null,
               array_map(function (AppleStateItemDtoInterface $dto) {
                   return $this->convertAppleStateItemDtoToAppleItemResponseDto($dto);
               }, $appleStateItemDtos)
        );
    }

    public function fallDown($userId, $appleId): GenerateApplesResponseDto
    {
        try {
            $currentUserId = $this->userRoleService->getCurrentUserId();
            if ($currentUserId !== $userId) {
                throw new ForbiddenHttpException(
                    'Ошибка прав доступа. Авторизированный пользователь не совпадает с пользователем в запросе.'
                );
            }

            $appleStateItemDto = $this->appleService->findActiveStateDtoByUserIdAndId($userId, $appleId);

            if (null === $appleStateItemDto) {
                throw new NotFoundHttpException(
                    sprintf('Яблоко #%d не найдено для пользователя %d', $appleId, $userId)
                );
            }

            $this->appleService->fallDown($userId, $appleStateItemDto->getId());
        } catch (Throwable $t) {
            Yii::error(
                sprintf(
                    'AppleManageService::fallDown error for user %d: %s. Trace: %s',
                    $userId,
                    $t->getMessage(),
                    $t->getTraceAsString(),
                )
            );

            $errorMessage = $this->generateErrorMessage($t);

            return new GenerateApplesResponseDto(
                ResponseStatusEnum::FAIL,
                $errorMessage,
                $this->findActiveDtosByUserId($userId)
            );
        }

        return new GenerateApplesResponseDto(
            ResponseStatusEnum::SUCCESS,
            null,
            $this->findActiveDtosByUserId($userId)
        );
    }

    public function eat($userId, $appleId, int $biteSizePercent): GenerateApplesResponseDto
    {
        try {
            $currentUserId = $this->userRoleService->getCurrentUserId();
            if ($currentUserId !== $userId) {
                throw new ForbiddenHttpException(
                    'Ошибка прав доступа. Авторизированный пользователь не совпадает с пользователем в запросе.'
                );
            }

            $appleStateItemDto = $this->appleService->findActiveStateDtoByUserIdAndId($userId, $appleId);

            if (null === $appleStateItemDto) {
                throw new NotFoundHttpException(
                    sprintf('Яблоко #%d не найдено для пользователя %d', $appleId, $userId)
                );
            }

            $this->appleService->eat($userId, $appleStateItemDto->getId(), $biteSizePercent);
        } catch (Throwable $t) {
            Yii::error(
                sprintf(
                    'AppleManageService::eat error for user %d: %s. Trace: %s',
                    $userId,
                    $t->getMessage(),
                    $t->getTraceAsString(),
                )
            );

            $errorMessage = $this->generateErrorMessage($t);

            return new GenerateApplesResponseDto(
                ResponseStatusEnum::FAIL,
                $errorMessage,
                $this->findActiveDtosByUserId($userId)
            );
        }

        return new GenerateApplesResponseDto(
            ResponseStatusEnum::SUCCESS,
            null,
            $this->findActiveDtosByUserId($userId)
        );
    }

    /**
     * @return AppleItemResponseDto[]
     */
    public function findActiveDtosByUserId(int $userId): array
    {
        $appleStateItemDtos = $this->appleService->findActiveStateDtosByUserId($userId);

        $filteredDtos = [];

        foreach ($appleStateItemDtos as $appleStateItemDto) {
            if (!$appleStateItemDto->isSpoil() && !empty($appleStateItemDto->getSizePercent())) {
                $filteredDtos[] = $appleStateItemDto;
            }
        }

        return array_map(function (AppleStateItemDtoInterface $dto) {
            return $this->convertAppleStateItemDtoToAppleItemResponseDto($dto);
        }, $filteredDtos);
    }

    private function convertAppleStateItemDtoToAppleItemResponseDto(
        AppleStateItemDtoInterface $appleStateItemDto
    ): AppleItemResponseDto {
        $statusLabel = match ($appleStateItemDto->getStatus()) {
            AppleStatusEnum::ON_TREE => 'На дереве',
            AppleStatusEnum::ON_GROUND => 'Лежит на земле',
        };

        $colorCode = $appleStateItemDto->getColor()->value;

        $size = rtrim(
                rtrim(
                    number_format($appleStateItemDto->getSizePercent(), 2, '.', ''),
                    '0'
                ),
                '.'
            ) . '%';

        $spoilMessage = null;

        if (null !== $appleStateItemDto->getFellAt()) {
            $hoursOnGround = (int) floor(
                (
                    (new DateTimeImmutable('now'))->getTimestamp()
                    - $appleStateItemDto->getFellAt()->getTimestamp()
                ) / 3600
            );

            if ($appleStateItemDto->isSpoil()) {
                $spoilMessage = sprintf(
                    'Испортилось, лежит на земле %d ч.',
                    $hoursOnGround
                );
            } else {
                $spoilMessage = sprintf(
                    'Не испортилось, лежит на земле %d ч.',
                    $hoursOnGround
                );
            }
        }

        return new AppleItemResponseDto(
            $appleStateItemDto->getId(),
            $statusLabel,
            $colorCode,
            $size,
            $appleStateItemDto->isCanFall(),
            $appleStateItemDto->isCanEat(),
            $appleStateItemDto->isSpoil(),
            $spoilMessage
        );
    }

    private function generateErrorMessage(Throwable $exception): string
    {
        if ($exception instanceof LogicException || $exception instanceof NotFoundHttpException || $exception instanceof ForbiddenHttpException) {
            $errorMessage = $exception->getMessage();
        } else {
            $errorMessage = 'Ошбика. Попробуйте еще раз.';
        }

        return $errorMessage;
    }
}
