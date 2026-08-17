<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Movie;

interface MovieRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function list(int $page, int $limit): array;
    public function save(Movie $movie): void;
    /**
     * @return mixed
     */
    public function find(int $id): mixed;
    /** 
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderby
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderby = []): mixed;
    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingImdbIds(array $ids): array;
    /**
     * @param array<Movie> $movies
     * @return void
     */
    public function bulkInsert(array $movies): void;
    /**
     * Used when the API is down or rate limit is exceeded to return local content.
     * @return array<Movie>
     */
    public function findPopularMovies(): array;

    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingTmdbIds(array $ids): array;
}
