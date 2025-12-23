<?php

namespace common\dtos\models;

use common\enums\AppleColorEnum;
use common\enums\AppleStatusEnum;
use DateTimeImmutable;

readonly class AppleDto
{
    public function __construct(
        private int $id,
        private int $userId,
        private AppleColorEnum $color,
        private float $sizePercent,
        private AppleStatusEnum $status,
        private DateTimeImmutable $appearedAt,
        private ?DateTimeImmutable $fellAt,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private bool $isArchive
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
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

    public function getAppearedAt(): DateTimeImmutable
    {
        return $this->appearedAt;
    }

    public function getFellAt(): ?DateTimeImmutable
    {
        return $this->fellAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isArchive(): bool
    {
        return $this->isArchive;
    }
}
