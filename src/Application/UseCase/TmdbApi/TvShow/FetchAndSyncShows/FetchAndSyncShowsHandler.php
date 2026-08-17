<?php

namespace App\Application\UseCase\TmdbApi\TvShow\FetchAndSyncShows;

use App\Application\DTO\TMDB\ShowDto;
use App\Domain\Exception\Show\PopularShowsException;
use App\Domain\Exception\Show\ShowsWithFiltersException;
use App\Application\Messenger\Media\Trigger\TriggerTmdbShowsSyncHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadPopularShows\LoadPopularShowsHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowsWithFilters\LoadShowsWithFiltersHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowsWithFilters\LoadShowsWithFiltersRequest;

final class FetchAndSyncShowsHandler
{

    public function __construct(
        private readonly LoadPopularShowsHandler $popularHandler,
        private readonly TriggerTmdbShowsSyncHandler $syncTrigger,
        private readonly LoadShowsWithFiltersHandler $filtersHandler,
    ) {}

    /**
     * @throws PopularShowsException
     * @return ShowDto[]
     */
    public function popular(): array
    {
        $content = $this->popularHandler->handle();

        $this->syncTrigger->handle();

        return $content;
    }

    /**
     * @param array<mixed> $filters
     * @throws ShowsWithFiltersException
     * @return ShowDto[]
     */
    public function withFilters(array $filters): array
    {
        $content = $this->filtersHandler->handle(new LoadShowsWithFiltersRequest($filters));

        $this->syncTrigger->handle($filters);

        return $content;
    }
}
