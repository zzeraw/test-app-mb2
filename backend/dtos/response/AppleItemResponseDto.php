<?php

declare(strict_types=1);

namespace backend\dtos\response;

readonly class AppleItemResponseDto
{
    public function __construct(
        private int $id,
        private string $statusLabel,
        private string $colorCode,
        private string $size,
        private bool $canFall,
        private bool $canEat,
        private bool $isSpoil,
        private ?string $spoilMessage
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatusLabel(): string
    {
        return $this->statusLabel;
    }

    public function getColorCode(): string
    {
        return $this->colorCode;
    }

    public function getSize(): string
    {
        return $this->size;
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

    public function getSpoilMessage(): ?string
    {
        return $this->spoilMessage;
    }
}
