<?php

namespace App\UI\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Application\UseCase\Links\ListMovieLinksHandler;
use App\Application\UseCase\Links\ListMovieLinksRequest;
use App\Application\UseCase\Links\ListShowLinksHandler;
use App\Application\UseCase\Links\ListShowLinksRequest;

#[OA\Tag('TMDB')]
#[Route('api/v1/tmdb/')]
final class LinksController extends AbstractController
{
    #[OA\Get(
        path: '/api/v1/tmdb/movies/links',
        summary: 'Get movie links',
        description: 'Returns links (streams/downloads) for a given movie id. If no links are found returns 204 No Content.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'query', description: 'Movie identifier', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response with links',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'object'))
            ),
            new OA\Response(
                response: 204,
                description: 'No content (no links found)'
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request (missing or invalid id)',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
        ]
    )]
    #[Route('movies/{id}/links', name: 'api.movies.links', methods: ['GET'])]
    public function getMovieLinks(string $id, ListMovieLinksHandler $handler): JsonResponse
    {
        $dto = new ListMovieLinksRequest($id);
        $results = $handler->handle($dto);
        return new JsonResponse(['results' => $results], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/v1/tmdb/shows/{id}/links',
        summary: 'Get show links',
        description: 'Returns the available links for a show, optionally filtered by season and episode.',
        parameters: [
            new OA\Parameter(name: 'season', in: 'query', description: 'Season number', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'episode', in: 'query', description: 'Episode number', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Links found', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'object'))),
            new OA\Response(response: 204, description: 'No links found'),
            new OA\Response(response: 400, description: 'Invalid request parameters'),
        ]
    )]
    #[Route('shows/{id}/links', name: 'api.v1.show.links', methods: ['GET'])]
    public function getShowLinks(string $id, Request $request, ListShowLinksHandler $handler): JsonResponse
    {
        $dto = new ListShowLinksRequest(
            $id,
            $request->query->get('season') ?? '',
            $request->query->get('episode') ?? ''
        );
        
        $results = $handler->handle($dto);
        return new JsonResponse($results, Response::HTTP_OK);
    }
}
