<?php

namespace App\Application\DataTransformer\TMDB;

use App\Application\DTO\TMDB\MovieCreditsDto;
use App\Application\DTO\TMDB\MovieDetailsDto;
use App\Application\DTO\TMDB\MovieDto;
use App\Application\DTO\TMDB\MovieImagesDto;

interface MovieDataTransformerInterface
{
    /**
     * @param array<mixed> $data
     */
    public function transformMovie(array $data): MovieDto;

    /**
     * @param array<mixed> $data
     */
    public function transformMovieDetails(array $data): MovieDetailsDto;

    /**
     * @param array<mixed> $data
     */
    public function transformMovieCredits(array $data): MovieCreditsDto;

    /**
     * @param array<mixed> $data
     */
    public function transformMovieImages(array $data): MovieImagesDto;
}
