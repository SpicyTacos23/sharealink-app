<?php

namespace App\Infrastructure\ApiCaller\Tmdb;

use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TmdbApiCallerService implements TmdbApiCallerInterface
{
    private const BASE = 'https://api.themoviedb.org/3/';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
        private readonly TmdbCacheKeyGeneratorInterface $cacheKey
    ) {}

    public function getPopularMovies(): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forMovies([]),
            self::BASE . "movie/popular"
        );
    }

    public function getMovieDetails(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_movie_" . $this->cacheKey->forMovies(['id' => $id]) . '_details',
            self::BASE . "movie/{$id}"
        );
    }

    public function getMovieCredits(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_movie_" . $this->cacheKey->forMovies(['id' => $id]) . '_credits',
            self::BASE . "movie/{$id}/credits"
        );
    }

    public function getPersonDetails(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_person_" . $this->cacheKey->forMovies(['id' => $id]) . '_details',
            self::BASE . "person/{$id}"
        );
    }

    public function getPersonFilmography(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_person_" . $this->cacheKey->forMovies(['id' => $id]) . '_filmography',
            self::BASE . "person/{$id}/movie_credits"
        );
    }

    public function getMovieGenres(): array
    {
        return $this->cachedRequest(
            "tmdb_movie_genres",
            self::BASE . "genre/movie/list"
        );
    }

    /**
     * @param array<mixed> $filters
     * @return array<mixed>
     */
    public function getMoviesWithFilters(array $filters): array
    {
        $url = self::BASE . "discover/movie?" . $this->buildQueryString($filters);

        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forMovies($filters),
            $url
        );
    }

    public function getPopularTvShows(): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows([]),
            self::BASE . "tv/popular"
        );
    }

    public function getShowDetails(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows(['id' => $id]) . '_details',
            self::BASE . "tv/{$id}"
        );
    }

    public function getShowCredits(string $id): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows(['id' => $id]) . '_credits',
            self::BASE . "tv/{$id}/credits"
        );
    }

    public function getShowEpisodes(string $id, int $seasonNumber): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows(['id' => $id, 'season_number' => $seasonNumber]) . '_episodes',
            self::BASE . "tv/{$id}/season/{$seasonNumber}"
        );
    }

    public function getShowImages(string $id, string $language = 'en'): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows(['id' => $id, 'language' => $language]) . '_images',
            self::BASE . "tv/{$id}/images?language={$language},null"
        );
    }

    public function getMovieImages(string $id, string $language = 'en'): array
    {
        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forMovies(['id' => $id, 'language' => $language]) . '_images',
            self::BASE . "movie/{$id}/images?language={$language},null"
        );
    }

    public function getShowsWithFilters(array $filters): array
    {
        $url = self::BASE . "discover/tv?" . $this->buildQueryString($filters);

        return $this->cachedRequest(
            "tmdb_" . $this->cacheKey->forShows($filters),
            $url
        );
    }

    public function getShowGenres(): array
    {
        return $this->cachedRequest(
            "tmdb_show_genres",
            self::BASE . "genre/tv/list"
        );
    }

    

    // -------------------------//
    // CORE REQUEST METHODS
    // -------------------------//

    /**
     * Builds query string using filters for movies and shows
     * @param array<mixed> $filters
     */
    private function buildQueryString(array $filters): string
    {
        $queryString = '';

        if (isset($filters['filters']['with_genres']) && is_array($filters['filters']['with_genres'])) {
            $filters['filters']['with_genres'] = implode(',', $filters['filters']['with_genres']);

            // Construir query string SIN codificar comas
            $queryParts = [];
            foreach ($filters['filters'] as $key => $value) {
                $queryParts[] = $key . '=' . $value; // no urlencode()
            }

            $queryString = implode('&', $queryParts);
        }

        return $queryString;
    }

    /** 
     * Uses Redis cache to find the Key, if not exists or expired makes a new API call.
     * @return array<string, mixed> 
     */
    private function cachedRequest(
        string $cacheKey,
        string $url
    ): array {

        if (!str_starts_with($url, 'http')) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
        
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($url) {
            $item->expiresAfter(18000);
            $response = $this->client->request('GET', $url, [
                'auth_bearer' => $_ENV['TMDB_API_KEY']
            ]);

            if ($response->getStatusCode() !== 200) {

                $item->expiresAfter(0);
                $this->logger->error($response->getContent(false));
                $errorContent = json_decode($response->getContent(false), true);

                if (is_array($errorContent) && isset($errorContent['message'])) {
                    $errorMessage = $errorContent['message'];
                } else {
                    $errorMessage = 'TMDB API error';
                }

                throw new \RuntimeException($errorMessage);
            }

            return $response->toArray();
        });
    }
}
