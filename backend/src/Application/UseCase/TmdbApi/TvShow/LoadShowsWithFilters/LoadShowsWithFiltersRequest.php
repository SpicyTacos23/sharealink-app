<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowsWithFilters;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadShowsWithFiltersRequest
{
    /**
     * @var array<string, string> $filters
     */
    #[Assert\NotBlank(message: 'api.tmdb.shows.filters')]
    public array $filters;

    /**
     * @param array<mixed> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
}