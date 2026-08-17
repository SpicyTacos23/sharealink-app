<?php

namespace App\Domain\ApiCaller\Tmdb;

interface TmdbApiCallerInterface
{
    /**
     * @return array<mixed>
     */
    public function getPopularMovies(): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getMovieDetails(string $id): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getMovieCredits(string $id): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getPersonDetails(string $id): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getPersonFilmography(string $id): array;

    /**
     * @return array<string, array<int, string>>
     */
    public function getMovieGenres(): array;

    /**
     * @param array<mixed> $filters
     * @return array<mixed>
     */
    public function getMoviesWithFilters(array $filters): array;

    /**
     * @return array<mixed>
     */
    public function getPopularTvShows(): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getShowDetails(string $id): array;

    /**
     * @param string $id
     * @return array<mixed>
     */
    public function getShowCredits(string $id): array;

    /**
     * @param string $id
     * @param int $seasonNumber
     * @return array<mixed>
     */
    public function getShowEpisodes(string $id, int $seasonNumber): array;

    /**
     * @param string $id
     * @param string $language
     * @return array<mixed>
     */
    public function getShowImages(string $id, string $language = 'en'): array;

    /**
     * @param string $id
     * @param string $language
     * @return array<mixed>
     */
    public function getMovieImages(string $id, string $language = 'en'): array;

    /**
     * @param array<mixed> $filters
     * @return array<mixed>
     */
    public function getShowsWithFilters(array $filters): array;

    /**
     * @return array<string, array<int, string>>
     */
    public function getShowGenres(): array;
}
