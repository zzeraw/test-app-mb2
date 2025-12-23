<?php

declare(strict_types=1);

namespace common\public_services;

use common\public_interfaces\AppleStateItemDtoInterface;

interface AppleServiceInterface
{
    /**
     * @return AppleStateItemDtoInterface[]
     */
    public function generate(int $userId): array;

    /**
     * @return AppleStateItemDtoInterface[]
     */
    public function findActiveStateDtosByUserId(int $userId): array;

    public function findActiveStateDtoByUserIdAndId($userId, $appleId): ?AppleStateItemDtoInterface;

    public function fallDown($userId, $appleId): void;

    public function eat(int $userId, int $appleId, int $biteSizePercent): void;
}
