<?php

declare(strict_types=1);

namespace backend\dtos\response;

use backend\enums\ResponseStatusEnum;

readonly class GenerateApplesResponseDto
{
    /**
     * @param AppleItemResponseDto[] $appleDtos
     */
    public function __construct(
        private ResponseStatusEnum $status,
        private ?string $message,
        private array $appleDtos,
    ) {
    }

    public function getStatus(): ResponseStatusEnum
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return AppleItemResponseDto[]
     */
    public function getAppleDtos(): array
    {
        return $this->appleDtos;
    }
}
