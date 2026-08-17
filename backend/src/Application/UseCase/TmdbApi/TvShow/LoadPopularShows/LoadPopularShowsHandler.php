<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadPopularShows;

use App\Application\DataTransformer\TMDB\ShowDataTransformerInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Show\PopularShowsException;
use App\Application\DTO\TMDB\ShowDto;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class LoadPopularShowsHandler
{
    public function __construct(
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly ShowDataTransformerInterface $dataTransformer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @throws PopularShowsException
     * @return ShowDto[]
     */
    public function handle(): array
    {
        try {
            $response = $this->apiCaller->getPopularTvShows();
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new PopularShowsException();
        }

        $shows = [];
        foreach ($response['results'] as $showData) {
            $shows[] = $this->dataTransformer->transformShow($showData);
        }
        return $shows;
    }
}
