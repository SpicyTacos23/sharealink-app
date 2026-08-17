<?php

namespace App\Application\DataTransformer\TMDB;

use App\Application\DTO\TMDB\GenreDto;

interface GenreDataTransformerInterface
{
    /**
     * @param array<string, mixed> $genre
     * @return GenreDto
     */
    public function transformGenre(array $genre): GenreDto;
}