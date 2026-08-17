<?php

namespace App\Domain\ApiCaller\Imdb;

interface ImdbApiCallerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getMovieDetails(string $id): array;
    /**
     * @return array<string, mixed>
     */
    public function getShowEpisodes(string $id, int $season): array;
    /**
     * @return array<string, mixed>
     */
    public function getShowSeasons(string $id): array;
    /**
     * @return array<string, mixed>
     * @param array<string, mixed> $filters
     */
    public function getMovies(array $filters): array;
    /**
     * @return array<string, mixed>
     * @param array<string, mixed> $filters
     */
    public function getShows(array $filters): array;
    /**
     * @return array<string, mixed>
     */
    public function getPerson(string $id): array;
    /**
     * @return array<string, mixed>
     */
    public function getPersonFilmography(string $id): array;
    /**
     * @return array<string, mixed>
     */
    public function getInterests(): array;
    /**
     * @return array<string, mixed>
     */
    public function searchTitle(string $searchTerm): array;
}
