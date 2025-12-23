<?php

declare(strict_types=1);

namespace common\public_interfaces;

use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use DateTimeImmutable;

interface AppleStateItemDtoInterface
{
    public function getId(): int;

    public function getColor(): AppleColorEnum;

    public function getSizePercent(): int;

    public function getStatus(): AppleStatusEnum;

    public function isCanFall(): bool;

    public function isCanEat(): bool;

    public function isSpoil(): bool;

    public function getAppearedAt(): DateTimeImmutable;

    public function getFellAt(): ?DateTimeImmutable;
}
