<?php

namespace App\Application\DTO\TMDB;

final class ShowDto
{
    /**
     * @param array<mixed> $genreIds
     * @param array<mixed> $originCountry
     */
    public function __construct(
        public readonly bool $adult,
        public readonly ?string $backdropPath,
        public readonly array $genreIds,
        public readonly int $id,
        public readonly array $originCountry,
        public readonly string $originalLanguage,
        public readonly string $originalName,
        public readonly string $overview,
        public readonly float $popularity,
        public readonly ?string $posterPath,
        public readonly ?string $firstAirDate,
        public readonly bool $softcore,
        public readonly string $name,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly string $type
    ) {}
}