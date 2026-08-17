<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class ShowSeasonDto
{
    /**
     * @param array<mixed> $episodes
     * @param array<mixed> $networks
     */
    public function __construct(
        public readonly string $_id,
        public readonly ?DateTime $airDate,
        public readonly array $episodes,
        public readonly string $name,
        public readonly array $networks,
        public readonly string $overview,
        public readonly int $id,
        public readonly string $posterPath,
        public readonly int $seasonNumber,
        public readonly float $voteAverage,
    ) {}
}
