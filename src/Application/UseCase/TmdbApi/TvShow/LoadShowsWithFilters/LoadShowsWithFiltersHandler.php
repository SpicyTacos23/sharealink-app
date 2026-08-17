<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowsWithFilters;

use App\Application\DataTransformer\TMDB\ShowDataTransformerInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Show\ShowsWithFiltersException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Application\DTO\TMDB\ShowDto;

final class LoadShowsWithFiltersHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly ShowDataTransformerInterface $dataTransformer
    ){}

    /**
     * @throws ShowsWithFiltersException
     * @return ShowDto[]
     */
    public function handle(LoadShowsWithFiltersRequest $request): array
    {
        $this->validator->validate($request);
        try {
            $filteredShows = $this->apiCaller->getShowsWithFilters($request->filters);
        }catch (RuntimeException $apiEx)
        {
            $this->logger->error($apiEx->getMessage());
            throw new ShowsWithFiltersException();

        }
        $shows = [];
        foreach ($filteredShows['results'] as $show) {
            $shows[] = $this->dataTransformer->transformShow($show);
        }
        
        return $shows;
    }
}