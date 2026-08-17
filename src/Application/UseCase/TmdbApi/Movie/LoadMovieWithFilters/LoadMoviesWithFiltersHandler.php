<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadMovieWithFilters;

use App\Application\DataTransformer\TMDB\MovieDataTransformerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Application\DTO\TMDB\MovieDto;
use App\Domain\Exception\Movie\MovieWithFiltersException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class LoadMoviesWithFiltersHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly MovieDataTransformerInterface $dataTransformer
    ) {}

    /**
     * @throws MovieWithFiltersException
     * @return MovieDto[]
     */
    public function handle(LoadMoviesWithFiltersRequest $request): array
    {
        $this->validator->validate($request);

        try {
            $filteredMovies = $this->apiCaller->getMoviesWithFilters($request->filters);
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new MovieWithFiltersException();
        }

        $movies = [];
        foreach ($filteredMovies['results'] as $movie) {
            $movies[] = $this->dataTransformer->transformMovie($movie);
        }

        return $movies;
    }
}
