<?php

namespace App\Service;

use App\Enum\MediaType;
use App\Interfaces\GetApiDataInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetApiData implements GetApiDataInterface
{
    private string $apiBaseUrl;

    public function __construct(
        private readonly HttpClientInterface $client,
        string $apiBaseUrl
    ) {
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/');
    }

    public function getMovies(array $filters): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/movies/popular');
        $content = json_decode($response->getContent(), true);

        return new JsonResponse($content['content'] ?? [], Response::HTTP_OK);
    }

    public function filterMovies(array $filters): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/movies/filter', [
            'query' => $filters
        ]);
        $movies = json_decode($response->getContent(), true);

        return new JsonResponse($movies['movies'] ?? [], Response::HTTP_OK);
    }

    public function filterShows(array $filters): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/shows/filter', [
            'query' => $filters
        ]);
        $shows = json_decode($response->getContent(), true);

        return new JsonResponse($shows ?? [], Response::HTTP_OK);
    }

    public function getPopularShows(): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/shows/popular');

        return new JsonResponse(json_decode($response->getContent(), true), Response::HTTP_OK);
    }

    public function getShows(array $filters): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/shows/popular');
        $shows = json_decode($response->getContent(), true);
        return new JsonResponse($shows, Response::HTTP_OK);
    }

    /**
     * Valid for movies and shows based on mediatype
     */
    public function getMediaDetails(string $id, MediaType $mediaType): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . "/api/v1/tmdb/{$mediaType->value}/{$id}/details");
        return new JsonResponse(json_decode($response->getContent(false), true));
    }

    public function getShowEpisodes(string $id, int $season): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . "/api/v1/tmdb/shows/{$id}/episodes", [
            'query' => [
                'season_number' => $season
            ]
        ]);

        return new JsonResponse(json_decode($response->getContent(false), true));
    }

    /**
     * @deprecated
     */
    public function getShowSeasons(string $id): JsonResponse
    {

        $response = $this->client->request(
            'GET',
            $this->apiBaseUrl . "/api/v1/tmdb/show/{$id}/seasons",
            [
                'json' => ['seasonNumber']
            ]
        );

        return new JsonResponse(json_decode($response->getContent(false), true));
    }

    public function getMovieLinks(string $id): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . "/api/v1/tmdb/movies/{$id}/links");
        return new JsonResponse(json_decode($response->getContent(), true));
    }

    public function getShowLinks(string $id, int $season, int $episode): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/show/links', [
            'json' => ['id' => $id, 'season' => $season, 'episode' => $episode]
        ]);

        return new JsonResponse(json_decode($response->getContent(), true));
    }

    public function getLinkDetails(int $id, string $authToken): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/media-file/link', [
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken,
                'Accept'        => 'application/json',
            ],
            'json' => ['id' => $id]
        ]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($response->getContent(), true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function getPersonDetails(string $id): JsonResponse
    {
        $request = $this->client->request('GET', $this->apiBaseUrl . "/api/v1/tmdb/person/{$id}/details");
        $response = json_decode($request->getContent(), true);

        if ($request->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse($response, Response::HTTP_OK);
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function getPersonFilmography(string $id): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . "/api/v1/tmdb/person/{$id}/filmography");

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($response->getContent(), true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    /**
     * @deprecated
     */
    public function getInterests(): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/imdb-dev/interests');

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($response->getContent(), true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function findMedia(string $query, string $mediaType): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/imdb-dev/find-media', [
            'query' => ['query' => $query, 'mediaType' => $mediaType]
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
        }

        return new JsonResponse(json_decode($response->getContent(), true));
    }

    public function getMovieGenres(): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/movies/genres');
        $content = json_decode($response->getContent(), true);
        return new JsonResponse($content['genres'] ?? [], Response::HTTP_OK);
    }

    public function getShowGenres(): JsonResponse
    {
        $response = $this->client->request('GET', $this->apiBaseUrl . '/api/v1/tmdb/shows/genres');
        $content = json_decode($response->getContent(), true);
        return new JsonResponse($content['genres'] ?? [], Response::HTTP_OK);
    }
}
