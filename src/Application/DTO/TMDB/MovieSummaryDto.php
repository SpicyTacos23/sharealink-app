<?php

namespace App\Application\DTO\TMDB;

/**
 * Used to show fewer fields from Movie
 */
final class MovieSummaryDto
{
    public function __construct(public int $movieId, public string $movieName) {}
}
