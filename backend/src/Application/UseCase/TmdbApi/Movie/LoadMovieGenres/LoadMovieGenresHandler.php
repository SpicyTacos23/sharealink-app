<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadMovieGenres;

use RuntimeException;
use Psr\Log\LoggerInterface;
use App\Application\DTO\TMDB\GenreDto;
use App\Domain\Exception\Movie\MovieGenresException;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Application\DataTransformer\TMDB\GenreDataTransformerInterface;

final class LoadMovieGenresHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly GenreDataTransformerInterface $dataTransformer,
    ) {}

    /**
     * @return GenreDto[]
     */
    public function handle(): array
    {
        try {
            $getGenres = $this->apiCaller->getMovieGenres();
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new MovieGenresException();
        }

        $genres = [];

        /**
         * @var array<string, mixed> $genre
         */
        foreach ($getGenres['genres'] as $genre) {
            $genres[] = $this->dataTransformer->transformGenre($genre);
        }

        return $genres;
    }
}
