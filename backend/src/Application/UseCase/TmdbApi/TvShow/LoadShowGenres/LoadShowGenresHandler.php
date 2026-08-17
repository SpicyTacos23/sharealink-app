<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowGenres;

use App\Application\DataTransformer\TMDB\GenreDataTransformerInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Application\DTO\TMDB\GenreDto;
use App\Domain\Exception\Show\ShowGenresException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class LoadShowGenresHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly GenreDataTransformerInterface $dataTransformer
    ) {}

    /**
     * @throws ShowGenresException
     * @return GenreDto[]
     */
    public function handle(): array
    {
        try {
            $getGenres = $this->apiCaller->getShowGenres();
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new ShowGenresException();
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
