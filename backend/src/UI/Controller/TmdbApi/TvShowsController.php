<?php

namespace App\UI\Controller\TmdbApi;

use RuntimeException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowGenres\LoadShowGenresHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowDetails\LoadShowDetailsHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowDetails\LoadShowDetailsRequest;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowEpisodes\LoadShowEpisodesHandler;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowEpisodes\LoadShowEpisodesRequest;
use App\Application\UseCase\TmdbApi\TvShow\FetchAndSyncShows\FetchAndSyncShowsHandler;
use App\Domain\Exception\Show\PopularShowsException;
use App\Domain\Exception\Show\ShowDetailsException;
use App\Domain\Exception\Show\ShowEpisodesException;
use App\Domain\Exception\Show\ShowGenresException;
use App\Domain\Exception\Show\ShowsWithFiltersException;

#[OA\Tag('TMDB')]
#[Route('api/v1/tmdb/shows/')]
final class TvShowsController extends AbstractController
{

    #[Route('popular', name: 'api.shows.popular', methods: ['GET'])]
    public function loadPopularTvShows(FetchAndSyncShowsHandler $orquester): JsonResponse
    {
        try {
            $shows = $orquester->popular();
        } catch (PopularShowsException $popularShowsException) {
            return new JsonResponse([
                'content' => [],
                'error_message' => $popularShowsException->getMessage()
            ], $popularShowsException->getCode());
        }
        return new JsonResponse(['content' => $shows], Response::HTTP_OK);
    }

    #[Route('{id}/details', name: 'api.shows.details', methods: ['GET'])]
    public function loadTvShowDetails(string $id, LoadShowDetailsHandler $handler): JsonResponse
    {
        try {
            $response = $handler->handle(new LoadShowDetailsRequest($id));
        } catch (ShowDetailsException $showDetailsEx) {
            return new JsonResponse(['error_message' => $showDetailsEx->getMessage()], $showDetailsEx->getCode());
        }
        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('genres', name: 'api.show.genres', methods: ['GET'])]
    public function loadTvShowsGenres(LoadShowGenresHandler $handler): JsonResponse
    {
        try {
            $genres = $handler->handle();
        } catch (ShowGenresException $genresException) {
            return new JsonResponse(
                ['genres' => [], 'error_message' => $genresException->getMessage()],
                $genresException->getCode()
            );
        }
        return new JsonResponse([
            'genres' => $genres
        ], Response::HTTP_OK);
    }

    #[Route('{id}/episodes', name: 'api.show.episodes', methods: ['GET'])]
    public function loadTvShowEpisodes(string $id, Request $request, LoadShowEpisodesHandler $handler): JsonResponse
    {
        $seasons = [];
        $seasonsNumber = $request->query->get('season_number') ?? null;
        if (is_null($seasonsNumber)) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Season not received'
            ], Response::HTTP_BAD_REQUEST);
        }
        try {
            $seasons = $handler->handle(new LoadShowEpisodesRequest($id, (int)$seasonsNumber));
        } catch (ShowEpisodesException $showEpisodesEx) {
            return new JsonResponse(['error_message' => $showEpisodesEx->getMessage()], $showEpisodesEx->getCode());
        }

        return new JsonResponse($seasons, Response::HTTP_OK);
    }

    #[Route('filter', name: 'api.shows.filter', methods: ['GET'])]
    public function loadTvShowsWithFilters(Request $request, FetchAndSyncShowsHandler $handler): JsonResponse
    {
        $filters = $request->query->all();
        try {
            $shows = $handler->withFilters($filters);
        } catch (ShowsWithFiltersException $showWithFiltersEx) {
            return new JsonResponse(['shows' => [], 'error_message' => $showWithFiltersEx->getMessage()], $showWithFiltersEx->getCode());
        }
        return new JsonResponse([
            'shows' => $shows
        ], Response::HTTP_OK);
    }

}
