<?php

declare(strict_types=1);

namespace backend\services;

use backend\dtos\response\AppleItemResponseDto;
use backend\enums\ResponseStatusEnum;
use common\dtos\models\AppleDto;
use common\enums\AppleStatusEnum;
use common\enums\TranslationCategoryEnum;
use common\public_interfaces\AppleDtoInterface;
use common\public_interfaces\AppleStateItemDtoInterface;
use Yii;
use Throwable;
use backend\dtos\response\GenerateApplesResponseDto;
use common\public_services\AppleServiceInterface;
use DateTimeImmutable;

readonly class AppleManageService
{
    public function __construct(
        private AppleServiceInterface $appleService,
    ) {
    }

    public function generate(int $userId): GenerateApplesResponseDto
    {
        $appleStateItemDtos = [];

        try {
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

            $errorMessage = Yii::t(TranslationCategoryEnum::NAME->value, 'Error creating apples. Please try again later.');

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

    /**
     * @return AppleItemResponseDto[]
     */
    public function findActiveDtosByUserId(int $userId): array
    {
        $appleStateItemDtos = $this->appleService->findActiveDtosByUserId($userId);

        return array_map(function (AppleStateItemDtoInterface $dto) {
            return $this->convertAppleStateItemDtoToAppleItemResponseDto($dto);
        }, $appleStateItemDtos);
    }

    private function convertAppleStateItemDtoToAppleItemResponseDto(
        AppleStateItemDtoInterface $appleStateItemDto
    ): AppleItemResponseDto {
        $statusLabel = match ($appleStateItemDto->getStatus()) {
            AppleStatusEnum::ON_TREE   => 'На дереве',
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
}
