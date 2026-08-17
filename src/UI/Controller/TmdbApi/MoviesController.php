<?php

namespace App\UI\Controller\TmdbApi;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Domain\Exception\Movie\PopularMoviesException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCase\TmdbApi\Movie\LoadMovieGenres\LoadMovieGenresHandler;
use App\Application\UseCase\TmdbApi\Movie\LoadMovieDetails\LoadMovieDetailsHandler;
use App\Application\UseCase\TmdbApi\Movie\LoadMovieDetails\LoadMovieDetailsRequest;
use App\Application\UseCase\TmdbApi\Movie\FetchAndSyncMovies\FetchAndSyncMoviesHandler;
use App\Domain\Exception\Movie\MovieDetailsException;
use App\Domain\Exception\Movie\MovieGenresException;
use App\Domain\Exception\Movie\MovieWithFiltersException;

#[OA\Tag('TMDB')]
#[Route('api/v1/tmdb/movies/')]
final class MoviesController extends AbstractController
{

    #[OA\Get(
        path: '/api/v1/tmdb/movies/popular',
        summary: 'Load first 30 popular movies',
        description: 'Fetches the most popular movies from TMDB using the /movie/popular endpoint. Returns the first 30 results ordered by popularity.',
        parameters: [],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Popular movies loaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'content',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid request or TMDB error')
        ]
    )]
    #[Route('popular', name: 'api.movies.popular', methods: ['GET'])]
    public function loadPopularMovies(FetchAndSyncMoviesHandler $orquester): JsonResponse
    {
        try {
            $content = $orquester->popular();
        } catch (PopularMoviesException $popularMovieException) {
            return new JsonResponse(
                [
                    'content' => [],
                    'error_message' => $popularMovieException->getMessage()
                ],
                $popularMovieException->getCode()
            );
        }
        return new JsonResponse(['content' => $content], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/v1/tmdb/movies/{id}/details',
        summary: 'Load detailed information for a movie',
        description: 'Fetches full movie details from TMDB including metadata, images, genres, runtime, and additional append_to_response fields.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'TMDB movie ID',
                schema: new OA\Schema(type: 'integer', example: 550)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Movie details loaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(property: 'movie', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Movie not found or TMDB error')
        ]
    )]
    #[Route('{id}/details', name: 'api.movies.details', methods: ['GET'])]
    public function loadMovieDetails(string $id, LoadMovieDetailsHandler $handler): JsonResponse
    {
        try {
            $response = $handler->handle(new LoadMovieDetailsRequest($id));
        } catch (MovieDetailsException $movieDetailsException) {
            return new JsonResponse([
                'movie' => [],
                'error_message' => $movieDetailsException->getMessage()
            ], $movieDetailsException->getCode());
        }
        return new JsonResponse(['movie' => $response], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/v1/tmdb/movies/genres',
        summary: 'Load all movie genres',
        description: 'Fetches the list of movie genres available in TMDB using the /genre/movie/list endpoint.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Genres loaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'genres',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 28),
                                    new OA\Property(property: 'name', type: 'string', example: 'Action')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'TMDB error while loading genres')
        ]
    )]
    #[Route('genres', name: 'api.movies.genres', methods: ['GET'])]
    public function loadMovieGenres(LoadMovieGenresHandler $handler): JsonResponse
    {
        try {
            $response = $handler->handle();
        } catch (MovieGenresException $movieGenresEx) {
            return new JsonResponse([
                'error' => true,
                'error_message' => $movieGenresEx->getMessage()
            ], $movieGenresEx->getCode());
        }

        return new JsonResponse(['genres' => $response], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/v1/tmdb/movies/filter',
        summary: 'Load movies using TMDB discover filters',
        description: 'Uses TMDB /discover/movie endpoint to filter movies. All filters must be passed inside the "filters" query object.',
        parameters: [
            new OA\Parameter(
                name: 'filters',
                in: 'query',
                required: false,
                description: 'Object containing TMDB discover filters.',
                style: 'deepObject',
                explode: true,
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'with_genres',
                            type: 'array',
                            description: 'Array of genre IDs',
                            items: new OA\Items(type: 'integer', example: 18)
                        ),
                        new OA\Property(
                            property: 'primary_release_year',
                            type: 'integer',
                            example: 2024,
                            description: 'Filter movies released in a specific year'
                        ),
                        new OA\Property(
                            property: 'vote_average.gte',
                            type: 'number',
                            example: 7.5,
                            description: 'Minimum vote average'
                        ),
                        new OA\Property(
                            property: 'vote_average.lte',
                            type: 'number',
                            example: 9.0,
                            description: 'Maximum vote average'
                        ),
                        new OA\Property(
                            property: 'sort_by',
                            type: 'string',
                            example: 'popularity.desc',
                            description: 'Sorting criteria supported by TMDB'
                        ),
                        new OA\Property(
                            property: 'with_original_language',
                            type: 'string',
                            example: 'en',
                            description: 'Filter by original language'
                        )
                    ],
                    example: [
                        'with_genres' => [18, 23],
                        'primary_release_year' => 2024,
                        'vote_average.gte' => 7.5,
                        'sort_by' => 'popularity.desc'
                    ]
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Movies filtered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'movies',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid filters or TMDB error')
        ]
    )]
    #[Route('filter', name: 'api.movies.filter', methods: ['GET'])]
    public function loadMovieWithFilters(Request $request, FetchAndSyncMoviesHandler $handler): JsonResponse
    {
        $filters = $request->query->all();
        try {
            $movies = $handler->withFilters($filters);
        } catch (MovieWithFiltersException $moviesWithFiltersEx) {
            return new JsonResponse([
                'error' => true,
                'error_message' => $moviesWithFiltersEx->getMessage()
            ]);
        }

        return new JsonResponse([
            'error' => false,
            'movies' => $movies
        ]);
    }

}
