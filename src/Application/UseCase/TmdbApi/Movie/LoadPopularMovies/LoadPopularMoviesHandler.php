<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadPopularMovies;

use TypeError;
use Psr\Log\LoggerInterface;
use App\Application\DTO\TMDB\MovieDto;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Movie\PopularMoviesException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Application\DataTransformer\TMDB\MovieDataTransformerInterface;

final class LoadPopularMoviesHandler
{

    public function __construct(
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly MovieDataTransformerInterface $dataTransformer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @return MovieDto[]
     */
    public function handle(): array
    {
        $movies = [];
        try {
            $response = $this->apiCaller->getPopularMovies();
            if (!isset($response['results']) || empty($response['results'])) {
                throw new PopularMoviesException('Empty response', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            foreach ($response['results'] as $movie) {
                try {
                    $movies[] = $this->dataTransformer->transformMovie($movie);
                } catch (TypeError $typeEx) {
                    $this->logger->warning("Error converting entity. Type Error!" . PHP_EOL . $typeEx->getMessage());
                }
            }
        } catch (TransportExceptionInterface $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new PopularMoviesException();
        }

        return $movies;
    }
}
