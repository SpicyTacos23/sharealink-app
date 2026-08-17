<?php

namespace App\Application\Encoder;

interface TmdbCacheKeyGeneratorInterface
{
    /**
     * @param array<mixed> $filters
     */
    public function forShows(array $filters): string;

    /**
     * @param array<mixed> $filters
     */
    public function forMovies(array $filters): string;

    /**
     * @param string $searchTerm
     */
    public function forSearch(string $searchTerm): string;
}