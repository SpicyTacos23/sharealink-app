<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadMovieWithFilters;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadMoviesWithFiltersRequest
{
    /**
     * @var array<string, string> $filters
     */
    #[Assert\NotBlank(message: 'api.tmdb.movie.filters')]
    public array $filters;

    /**
     * @param array<mixed> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
}
