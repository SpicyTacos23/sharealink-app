<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowDetails;

use App\Application\DataTransformer\TMDB\GenreDataTransformerInterface;
use App\Application\DataTransformer\TMDB\ShowDataTransformerInterface;
use App\Application\UseCase\TmdbApi\TvShow\LoadShowDetails\LoadShowDetailsRequest;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Show\ShowDetailsException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LoadShowDetailsHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly ShowDataTransformerInterface $dataTransformer,
        private readonly GenreDataTransformerInterface $genreDataTransformer
    ) {}

    /**
     * @return array<mixed>
     */
    public function handle(LoadShowDetailsRequest $request): array
    {
        //Validate request
        $this->validator->validate($request);

        //Load data
        try {
            $showDetails = $this->apiCaller->getShowDetails($request->id);
            $showCredits = $this->apiCaller->getShowCredits($request->id);
            $showImages = $this->apiCaller->getShowImages($request->id, $showDetails['original_language'] ?? null);
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new ShowDetailsException();
        }

        //Filter by directors
        $directors = array_filter(
            $showCredits['crew'],
            fn($member) => $member['job'] === 'Director'
        );
        //Use only first 10
        $credits = [
            'id' => $showCredits['id'],
            'cast' => array_slice($showCredits['cast'], 0, 10),
            'crew' => array_slice($directors, 0, 5)
        ];

        $genres = [];
        foreach ($showDetails['genres'] as $genre) {
            $genres[] = $this->genreDataTransformer->transformGenre($genre);
        }

        //Model data
        $details = [
            'details' => $this->dataTransformer->transformShowDetails($showDetails),
            'credits' => $this->dataTransformer->transformShowCredits($credits),
            'images' => $this->dataTransformer->transformShowImages($showImages),
            'genres' => $genres
        ];

        //Return
        return $details;
    }
}
