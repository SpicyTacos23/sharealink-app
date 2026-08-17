<?php

namespace App\Application\Messenger\Statistics\Message;

final class PageVisitedMessage
{
    public function __construct(
        public readonly string $path,
        public readonly \DateTimeImmutable $visitedAt,
        public readonly ?string $ip,
        public readonly ?string $userAgent,
        public readonly ?string $referer,
    ) {
    }
}
