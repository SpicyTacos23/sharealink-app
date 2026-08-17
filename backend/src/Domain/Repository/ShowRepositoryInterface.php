<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Show;

interface ShowRepositoryInterface
{
    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingTmdbIds(array $ids): array;

    /**
     * @param array<mixed> $shows
     */
    public function bulkInsert(array $shows): void;
    
    /**
     * Fallback for API error
     * @return array<Show>
     */
    public function findPopularShows(): array;

    /** 
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderby
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderby = []): mixed;
}
