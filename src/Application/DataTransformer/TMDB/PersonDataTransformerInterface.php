<?php

namespace App\Application\DataTransformer\TMDB;

use App\Application\DTO\TMDB\PersonCreditsDto;
use App\Application\DTO\TMDB\PersonDetailsDto;

interface PersonDataTransformerInterface
{
    /**
     * @param array<mixed> $data
     */
    public function transformPersonDetails(array $data): PersonDetailsDto;

    /**
     * @param array<mixed> $data
     */
    public function transformPersonCredits(array $data): PersonCreditsDto;

}
