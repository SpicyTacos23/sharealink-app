<?php

namespace App\Application\DTO\TMDB;

final class MovieImagesDto
{
    /**
     * @param string $_id
     * @param ImageDto[] $backdrops
     * @param ImageDto[] $logos
     * @param ImageDto[] $posters
     */
    public function __construct(
        public readonly string $_id,
        public readonly array $backdrops,
        public readonly array $logos,
        public readonly array $posters
    ) {}
}
