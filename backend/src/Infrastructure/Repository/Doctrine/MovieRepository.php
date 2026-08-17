<?php

namespace App\Infrastructure\Repository\Doctrine;

use App\Domain\Entity\Movie;
use App\Domain\Repository\MovieRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository implements MovieRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /** @return array<string, array<mixed>> */
    public function list(int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $paginator = new Paginator($qb);

        $total = count($paginator);
        $totalPages = (int) ceil($total / $limit);

        return [
            'items' => $paginator->getQuery()->getArrayResult(),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total_items' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    public function save(Movie $movie): void
    {
        $this->getEntityManager()->persist($movie);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingImdbIds(array $ids): array
    {
        $rows = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.movieId')
            ->from(Movie::class, 's')
            ->where('s.movieId IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'movieId');
    }

    public function bulkInsert(array $movies): void
    {
        foreach ($movies as $movie) {
            $this->getEntityManager()->persist($movie);
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function findPopularMovies(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.title AS primaryTitle')
            ->addSelect('m.movieId AS id')
            ->addSelect("'movie' AS type")
            ->addSelect('m.movieImage AS primaryImage')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<int> $ids
     * @return array<int>
     */
    public function findExistingTmdbIds(array $ids): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.movieId')
            ->where('m.movieId IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
