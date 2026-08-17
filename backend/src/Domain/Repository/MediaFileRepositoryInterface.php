<?php

namespace App\Domain\Repository;

use App\Domain\Entity\MediaFile;
use App\Domain\Entity\Movie;
use App\Domain\Enum\MediaType;

interface MediaFileRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function list(int $page, int $limit): array;

    public function save(MediaFile $mediaFile): void;

    public function saveMovieAndMedia(Movie $movie, MediaFile $media): void;

    public function findLink(int $id): ?MediaFile;
    
    /**
     * @return array<mixed>
     */
    public function findLinkByMediaId(int $mediaId, MediaType $mediaType): array;
}