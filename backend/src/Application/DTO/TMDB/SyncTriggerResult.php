<?php

namespace App\Application\DTO\TMDB;

final class SyncTriggerResult
{
    private function __construct(
        public readonly bool $dispatched,
        public readonly ?int $retryAfterSeconds = null,
        public readonly ?\DateTimeImmutable $nextAvailableAt = null,
    ) {}

    public static function dispatched(): self
    {
        return new self(dispatched: true);
    }

    public static function alreadyRunning(
        ?int $retryAfterSeconds = null,
        ?\DateTimeImmutable $nextAvailableAt = null,
    ): self {
        return new self(
            dispatched: false,
            retryAfterSeconds: $retryAfterSeconds,
            nextAvailableAt: $nextAvailableAt,
        );
    }

    public function isAlreadyRunning(): bool
    {
        return !$this->dispatched;
    }

    /**
     * @return array{
     *     dispatched: bool,
     *     retryAfterSeconds: int|null,
     *     nextAvailableAt: string|null
     * }
     */

    public function toArray(): array
    {
        return [
            'dispatched'        => $this->dispatched,
            'retryAfterSeconds' => $this->retryAfterSeconds,
            'nextAvailableAt'   => $this->nextAvailableAt?->format(DATE_ATOM),
        ];
    }
}
