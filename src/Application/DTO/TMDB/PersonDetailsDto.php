<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class PersonDetailsDto
{
    /**
     * @param array<mixed> $alsoKnownAs
     */
    public function __construct(
        public readonly int $id,
        public readonly bool $adult,
        public readonly array $alsoKnownAs,
        public readonly ?string $biography,
        public readonly ?DateTime $birthday,
        public readonly ?DateTime $deathday,
        public readonly int $gender,
        public readonly ?string $homepage,
        public readonly string $imdbId,
        public readonly string $knownForDepartment,
        public readonly string $name,
        public readonly ?string $placeOfBirth,
        public readonly float $popularity,
        public readonly ?string $profilePath,
    ) {}
}