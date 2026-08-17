<?php

namespace App\Infrastructure\DataTransformer\TMDB;

use App\Application\DataTransformer\TMDB\GenreDataTransformerInterface;
use App\Application\DTO\TMDB\GenreDto;

final class GenreDataTransformerService implements GenreDataTransformerInterface
{
    /**
     * @param array{id: int, name: string} $genre
     * @return GenreDto
     */
    public function transformGenre(array $genre): GenreDto
    {
        return new GenreDto(
            $genre['id'],
            $genre['name']
        );
    }
}