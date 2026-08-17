<?php

namespace App\Application\DTO\TMDB;

final class CastMemberDto
{
    public function __construct(
        public readonly int $id,
        public readonly bool $adult,
        public readonly int $gender,
        public readonly string $knownForDepartment,
        public readonly string $name,
        public readonly string $originalName,
        public readonly float $popularity,
        public readonly ?string $profilePath,
        public readonly int $castId,
        public readonly string $character,
        public readonly string $creditId,
        public readonly int $order,
    ) {}
}
