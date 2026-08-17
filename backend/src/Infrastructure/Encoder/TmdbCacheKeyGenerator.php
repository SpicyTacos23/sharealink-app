<?php

namespace App\Infrastructure\Encoder;

use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;

final class TmdbCacheKeyGenerator implements TmdbCacheKeyGeneratorInterface
{
    public function forShows(array $filters): string
    {
        $this->sortRecursive($filters);
        return 'shows_' . md5(json_encode($filters, JSON_THROW_ON_ERROR));
    }

    public function forMovies(array $filters): string
    {
        $this->sortRecursive($filters);
        return 'movies_' . md5(json_encode($filters, JSON_THROW_ON_ERROR));
    }
    
    public function forSearch(string $searchTerm): string
    {
        return 'search_' . md5($searchTerm);
    }

    /**
     * @param array<mixed> $array
     */
    private function sortRecursive(array &$array): void
    {
        ksort($array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortRecursive($value);
            }
        }
    }
}