<?php

namespace App\Application\UseCase\TmdbApi\Movie\FetchAndSyncMovies;

use App\Application\DTO\TMDB\MovieDto;
use App\Domain\Exception\Movie\MovieWithFiltersException;
use App\Application\Messenger\Media\Trigger\TriggerTmdbMoviesSyncHandler;
use App\Application\UseCase\TmdbApi\Movie\LoadPopularMovies\LoadPopularMoviesHandler;
use App\Application\UseCase\TmdbApi\Movie\LoadMovieWithFilters\LoadMoviesWithFiltersHandler;
use App\Application\UseCase\TmdbApi\Movie\LoadMovieWithFilters\LoadMoviesWithFiltersRequest;

final class FetchAndSyncMoviesHandler
{
    public function __construct(
        private readonly LoadPopularMoviesHandler $popularHandler,
        private readonly TriggerTmdbMoviesSyncHandler $syncTrigger,
        private readonly LoadMoviesWithFiltersHandler $filtersHandler,
    ) {}

    /**
     * @return MovieDto[]
     */
    public function popular(): array
    {
        $response = $this->popularHandler->handle();

        $this->syncTrigger->handle([]);

        return $response;
    }

    /**
     * @param array<mixed> $filters
     * @throws MovieWithFiltersException
     * @return MovieDto[]
     */
    public function withFilters(array $filters): array
    {
        $response = $this->filtersHandler->handle(new LoadMoviesWithFiltersRequest($filters));

        $this->syncTrigger->handle($filters);

        return $response;
    }
}
