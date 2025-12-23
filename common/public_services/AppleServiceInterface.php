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
    public function findActiveDtosByUserId(int $userId): array;
}
