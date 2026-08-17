<?php

namespace App\UI\Controller\TmdbApi;

use App\Application\UseCase\TmdbApi\Person\LoadPersonDetails\LoadPersonDetailsHandler;
use App\Application\UseCase\TmdbApi\Person\LoadPersonDetails\LoadPersonDetailsRequest;
use App\Application\UseCase\TmdbApi\Person\LoadPersonFilmography\LoadPersonFilmographyHandler;
use App\Application\UseCase\TmdbApi\Person\LoadPersonFilmography\LoadPersonFilmographyRequest;
use App\Domain\Exception\Person\PersonDetailsException;
use App\Domain\Exception\Person\PersonFilmographyException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag('TMDB')]
#[Route('api/v1/tmdb/person/')]
final class PersonController extends AbstractController
{
    #[Route('{id}/details', name: 'api.person.details', methods: ['GET'])]
    public function loadPersonDetails(string $id, LoadPersonDetailsHandler $handler): JsonResponse
    {
        try {
            $response = $handler->handle(new LoadPersonDetailsRequest($id));
        } catch (PersonDetailsException $personDetailsException) {
            return new JsonResponse([
                'error_message' => $personDetailsException->getMessage()
            ], $personDetailsException->getCode());
        }
        return new JsonResponse($response);
    }

    #[Route('{id}/filmography', name: 'api.person.filmography', methods: ['GET'])]
    public function loadPersonFilmography(string $id, LoadPersonFilmographyHandler $handler): JsonResponse
    {
        try {
            $response = $handler->handle(new LoadPersonFilmographyRequest($id));
        } catch (PersonFilmographyException $filmographyException) {
            return new JsonResponse([
                'error' => true,
                'error_message' => $filmographyException->getMessage()
            ], $filmographyException->getCode());
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
