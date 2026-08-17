<?php

namespace App\Application\DataTransformer\TMDB;

use App\Application\DTO\TMDB\ShowCreditsDto;
use App\Application\DTO\TMDB\ShowDetailsDto;
use App\Application\DTO\TMDB\ShowDto;
use App\Application\DTO\TMDB\ShowImagesDto;
use App\Application\DTO\TMDB\ShowSeasonDto;

interface ShowDataTransformerInterface
{
    /**
     * @param array<mixed> $data
     * @return ShowDto
     */
    public function transformShow(array $data): ShowDto;

    /**
     * @param array<mixed> $data
     */
    public function transformShowDetails(array $data): ShowDetailsDto;

    /**
     * @param array<mixed> $data
     */
    public function transformShowCredits(array $data): ShowCreditsDto;

    /**
     * @param array<mixed> $data
     */
    public function transformShowSeasons(array $data): ShowSeasonDto;

    /**
     * @param array<mixed> $data
     */
    public function transformShowImages(array $data): ShowImagesDto;
}
