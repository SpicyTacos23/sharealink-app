<?php

namespace App\Application\DTO\TMDB;

final class ShowCreditsDto
{
    /**
     * @param CastMemberDto[] $cast
     * @param CrewMemberDto[] $crew
     */
    public function __construct(
        public readonly int $id,
        public readonly array $cast,
        public readonly array $crew,
    ) {}
}
