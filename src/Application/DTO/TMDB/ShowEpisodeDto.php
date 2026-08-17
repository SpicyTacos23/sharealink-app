<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class ShowEpisodeDto
{
    /**
     * @param array<mixed> $crew
     * @param array<mixed> $guestStars
     */
    public function __construct(
        public readonly int $id,
        public readonly ?DateTime $airDate,
        public readonly int $episodeNumber,
        public readonly string $episodeType,
        public readonly string $name,
        public readonly string $overview,
        public readonly string $productionCode,
        public readonly ?int $runtime,
        public readonly int $seasonNumber,
        public readonly int $showId,
        public readonly string $stillPath,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly array $crew,
        public readonly array $guestStars,
    ) {}
}
