<?php

namespace App\Infrastructure\Repository\Doctrine;

use App\Domain\Entity\MediaFile;
use App\Domain\Entity\Movie;
use App\Domain\Enum\MediaType;
use App\Domain\Repository\MediaFileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaFile>
 */
class MediaFileRepository extends ServiceEntityRepository implements MediaFileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaFile::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function list(int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('mf')
            ->orderBy('mf.id', 'ASC')
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

    public function save(MediaFile $mediaFile): void
    {
        $this->getEntityManager()->persist($mediaFile);
        $this->getEntityManager()->flush();
    }

    public function saveMovieAndMedia(Movie $movie, MediaFile $media): void
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        $em->persist($movie);
        $em->flush();
        $media->setMovie($movie);
        $em->persist($media);

        $em->flush();
        $em->commit();
    }

    public function findLink(int $linkId): ?MediaFile
    {
        return $this->findOneBy(['id' => $linkId]);
    }

    /**
     * @return MediaFile[]
     */
    public function findLinkByMediaId(int $mediaId, MediaType $mediaType): array
    {
        return $this->createQueryBuilder('m')
        ->where('m.movie = :id')
        ->andWhere('m.mediaType = :type')
        ->setParameter('id', $mediaId)
        ->setParameter('type', $mediaType->value)
        ->getQuery()
        ->getResult();
    }
     
}
