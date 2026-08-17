<?php

namespace App\Application\DTO\TMDB;

final class CrewMemberDto
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
        public readonly string $creditId,
        public readonly string $department,
        public readonly string $job,
    ) {}
}
