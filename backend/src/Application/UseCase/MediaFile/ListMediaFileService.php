<?php

namespace App\Application\UseCase\MediaFile;

use App\Domain\Entity\MediaFile;
use App\Domain\Repository\MediaFileRepositoryInterface;

class ListMediaFileService
{
    public function __construct(private readonly MediaFileRepositoryInterface $repository) {}

    /**
     * @return array<string, mixed>
     */
    public function list(int $page, int $limit): array
    {
        return $this->repository->list($page, $limit);
    }

    public function findLink(int $linkId): ?MediaFile
    {
        return $this->repository->findLink($linkId);
    }
}
