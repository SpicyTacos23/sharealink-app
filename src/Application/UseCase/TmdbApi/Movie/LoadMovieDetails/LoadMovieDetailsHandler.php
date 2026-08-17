<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadMovieDetails;

use RuntimeException;
use Psr\Log\LoggerInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Movie\MovieDetailsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Application\DataTransformer\TMDB\GenreDataTransformerInterface;
use App\Application\DataTransformer\TMDB\MovieDataTransformerInterface;

final class LoadMovieDetailsHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly MovieDataTransformerInterface $dataTransformer,
        private readonly GenreDataTransformerInterface $genreDataTransformer
    ) {}

    /**
     * @return array{
     *     details: \App\Application\DTO\TMDB\MovieDetailsDto,
     *     credits: \App\Application\DTO\TMDB\MovieCreditsDto,
     *     images: \App\Application\DTO\TMDB\MovieImagesDto,
     *     genres: array<\App\Application\DTO\TMDB\GenreDto>
     * }
     */

    public function handle(LoadMovieDetailsRequest $request): array
    {
        //Validate request
        $this->validator->validate($request);

        //Load data
        try {
            $movieDetails = $this->apiCaller->getMovieDetails($request->id);
            $movieCredits = $this->apiCaller->getMovieCredits($request->id);
            $movieImages = $this->apiCaller->getMovieImages($request->id);
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new MovieDetailsException();
        }

        //Filter by directors
        $directors = array_filter(
            $movieCredits['crew'],
            fn($member) => $member['job'] === 'Director'
        );

        //Use only first 10
        $credits = [
            'id' => $movieCredits['id'],
            'cast' => array_slice($movieCredits['cast'], 0, 10),
            'crew' => array_slice($directors, 0, 5)
        ];

        $genres = [];
        foreach ($movieDetails['genres'] as $genre) {
            $genres[] = $this->genreDataTransformer->transformGenre($genre);
        }

        //Model data
        $movie = [
            'details' => $this->dataTransformer->transformMovieDetails($movieDetails),
            'credits' => $this->dataTransformer->transformMovieCredits($credits),
            'images' => $this->dataTransformer->transformMovieImages($movieImages),
            'genres' => $genres
        ];

        //Return
        return $movie;
    }
}
