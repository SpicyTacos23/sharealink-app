<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class MovieDetailsDto
{
    /**
     * @param array<mixed> $genres
     * @param array<mixed> $productionCompanies
     * @param array<mixed> $productionCountries
     * @param array<mixed> $originCountry
     * @param array<mixed> $spokenLanguages
     * @param array<mixed> $belongsToCollection
     */
    public function __construct(
        public readonly string $id,
        public readonly string $imdbId,
        public readonly string $title,
        public readonly string $originalTitle,
        public readonly string $originalLanguage,
        public readonly string $overview,
        public readonly string $homepage,
        public readonly string $status,
        public readonly string $tagline,
        public readonly bool $adult,
        public readonly string $backdropPath,
        public readonly array $genres,
        public readonly string $posterPath,
        public readonly ?DateTime $releaseDate,
        public readonly int $runtime,
        public readonly bool $video,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly int $budget,
        public readonly int $revenue,
        public readonly array $productionCompanies,
        public readonly array $productionCountries,
        public readonly array $originCountry,
        public readonly array $spokenLanguages,
        public readonly array $belongsToCollection,
        public readonly string $mediaType = 'movies'
    ) {}
}
