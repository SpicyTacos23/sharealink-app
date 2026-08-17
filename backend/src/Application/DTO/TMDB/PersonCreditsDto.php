<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class PersonCreditsDto
{
    public function __construct(
        public readonly string $character,
        public readonly string $creditId,
        public readonly int $order,
        public readonly string $mediaType,
    ) {}
}