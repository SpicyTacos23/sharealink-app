<?php

namespace App\Infrastructure\Repository\Doctrine;

use App\Domain\Entity\Show;
use App\Domain\Repository\ShowRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Show>
 */
class ShowRepository extends ServiceEntityRepository implements ShowRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Show::class);
    }

    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingTmdbIds(array $ids): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.movieId')
            ->where('s.movieId IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function bulkInsert(array $shows): void
    {
        foreach ($shows as $show) {
            $this->getEntityManager()->persist($show);
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function findPopularShows(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.title AS primaryTitle')
            ->addSelect('m.imdbId AS id')
            ->addSelect("'show' AS type")
            ->addSelect('m.showImage AS primaryImage')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }
}
