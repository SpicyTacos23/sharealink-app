<?php

namespace App\Application\UseCase\TmdbApi\Person\LoadPersonFilmography;

use App\Application\DataTransformer\TMDB\MovieDataTransformerInterface;
use App\Application\DataTransformer\TMDB\PersonDataTransformerInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Person\PersonFilmographyException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LoadPersonFilmographyHandler
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly MovieDataTransformerInterface $dataTransformerMovie,
        private readonly PersonDataTransformerInterface $dataTransformerPerson
    ) {}

    /**
     * @return array<mixed>
     */
    public function handle(LoadPersonFilmographyRequest $request): array
    {
        $this->validator->validate($request);

        try {
            $response = $this->apiCaller->getPersonFilmography($request->id);
        } catch (RuntimeException $apiErr) {
            $this->logger->error($apiErr->getMessage());
            throw new PersonFilmographyException();
        }

        $movies = [];
        foreach ($response['cast'] as $key => $film) {
            if (($film['media_type'] ?? 'movie') !== 'movie') {
                continue;
            }

            $movies[$key]['movie'] = $this->dataTransformerMovie->transformMovie($film);
            $movies[$key]['cast']  = $this->dataTransformerPerson->transformPersonCredits($film);
        }


        return $movies;
    }
}
