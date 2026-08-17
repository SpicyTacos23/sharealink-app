<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class MovieDto
{
     /**
     * @param int[] $genres
     */
    public function __construct(
        public readonly bool $adult,
        public readonly string $backdropPath,
        public readonly array $genres,
        public readonly string $id,
        public readonly string $title,
        public readonly string $originalTitle,
        public readonly string $originalLanguage,
        public readonly string $overview,
        public readonly int $popularity,
        public readonly string $posterPath,
        public readonly DateTime $releaseDate,
        public readonly bool $softcore,
        public readonly bool $video,
        public readonly int $voteAverage,
        public readonly int $voteCount,
        public readonly string $type = 'movie'
    ) {}
}