<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\MediaType;

interface GetApiDataInterface
{
    public function getMovies(array $filters): JsonResponse;
    public function getPopularShows(): JsonResponse;
    public function getShows(array $filters): JsonResponse;
    public function getMediaDetails(string $id, MediaType $mediaType): JsonResponse;
    public function getPersonDetails(string $id): JsonResponse;
    public function getMovieLinks(string $id): JsonResponse;
    public function getShowLinks(string $id, int $season, int $episode): JsonResponse;
    public function getShowEpisodes(string $id, int $season): JsonResponse;
    public function getShowSeasons(string $id): JsonResponse;
    public function getLinkDetails(int $id, string $authToken): JsonResponse;
    public function getPersonFilmography(string $id): JsonResponse;
    /**
     * @deprecated
     */
    public function getInterests(): JsonResponse;
    public function getMovieGenres(): JsonResponse;
    public function getShowGenres(): JsonResponse;
    public function findMedia(string $query, string $mediaType): JsonResponse;
    public function filterMovies(array $filters): JsonResponse;
    public function filterShows(array $filters): JsonResponse;
}