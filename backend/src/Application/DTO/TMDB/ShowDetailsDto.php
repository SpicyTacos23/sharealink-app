<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class ShowDetailsDto
{
    /**
     * @param array<mixed> $createdBy
     * @param array<mixed> $episodeRunTime
     * @param array<mixed> $genres
     * @param array<mixed> $languages
     * @param array<mixed> $lastEpisodeToAir
     * @param array<mixed> $nextEpisodeToAir
     * @param array<mixed> $networks
     * @param array<mixed> $originCountry
     * @param array<mixed> $productionCompanies
     * @param array<mixed> $productionCountries
     * @param array<mixed> $seasons
     * @param array<mixed> $spokenLanguages
     */
    public function __construct(
        public readonly int $id,
        public readonly bool $adult,
        public readonly string $backdropPath,
        public readonly array $createdBy,
        public readonly array $episodeRunTime,
        public readonly ?DateTime $firstAirDate,
        public readonly array $genres,
        public readonly string $homepage,
        public readonly bool $inProduction,
        public readonly array $languages,
        public readonly ?DateTime $lastAirDate,
        public readonly array $lastEpisodeToAir,
        public readonly string $name,
        public readonly array $nextEpisodeToAir,
        public readonly array $networks,
        public readonly int $numberOfEpisodes,
        public readonly int $numberOfSeasons,
        public readonly array $originCountry,
        public readonly string $originalLanguage,
        public readonly string $originalName,
        public readonly string $overview,
        public readonly float $popularity,
        public readonly string $posterPath,
        public readonly array $productionCompanies,
        public readonly array $productionCountries,
        public readonly array $seasons,
        public readonly bool $softcore,
        public readonly array $spokenLanguages,
        public readonly string $status,
        public readonly string $tagline,
        public readonly string $type,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly string $mediaType = 'shows'
    ) {}
}
