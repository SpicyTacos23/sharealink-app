<?php

namespace App\Application\DTO\TMDB;

use DateTime;

final class ImageDto
{
    public function __construct(
        public readonly float $aspectRatio,
        public readonly int $height,
        public readonly string $iso,
        public readonly string $filePath,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly int $width,
    ) {}
}
