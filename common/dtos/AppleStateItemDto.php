<?php

declare(strict_types=1);

namespace common\dtos;

use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use common\public_interfaces\AppleStateItemDtoInterface;
use DateTimeImmutable;

readonly class AppleStateItemDto implements AppleStateItemDtoInterface
{
    public function __construct(
        private int $id,
        private AppleColorEnum $color,
        private float $sizePercent,
        private AppleStatusEnum $status,
        private bool $canFall,
        private bool $canEat,
        private bool $isSpoil,
        private DateTimeImmutable $appearedAt,
        private ?DateTimeImmutable $fellAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getColor(): AppleColorEnum
    {
        return $this->color;
    }

    public function getSizePercent(): float
    {
        return $this->sizePercent;
    }

    public function getStatus(): AppleStatusEnum
    {
        return $this->status;
    }

    public function isCanFall(): bool
    {
        return $this->canFall;
    }

    public function isCanEat(): bool
    {
        return $this->canEat;
    }

    public function isSpoil(): bool
    {
        return $this->isSpoil;
    }

    public function getAppearedAt(): DateTimeImmutable
    {
        return $this->appearedAt;
    }

    public function getFellAt(): ?DateTimeImmutable
    {
        return $this->fellAt;
    }
}
