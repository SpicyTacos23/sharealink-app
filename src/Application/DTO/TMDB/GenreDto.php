<?php

namespace App\Application\DTO\TMDB;

final class GenreDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}
}