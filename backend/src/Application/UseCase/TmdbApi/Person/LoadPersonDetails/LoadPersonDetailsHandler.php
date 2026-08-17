<?php

namespace App\Application\UseCase\TmdbApi\Person\LoadPersonDetails;

use App\Application\DataTransformer\TMDB\PersonDataTransformerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\Exception\Person\PersonDetailsException;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Application\DTO\TMDB\PersonDetailsDto;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class LoadPersonDetailsHandler
{

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly PersonDataTransformerInterface $dataTransformer,
        private readonly LoggerInterface $logger

    ) {}

    public function handle(LoadPersonDetailsRequest $request): PersonDetailsDto
    {
        $this->validator->validate($request);
        try {
            $response = $this->apiCaller->getPersonDetails($request->id);
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new PersonDetailsException();
        }
        $person = $this->dataTransformer->transformPersonDetails($response);
        return $person;
    }
}
