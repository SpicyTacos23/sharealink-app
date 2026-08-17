<?php

namespace App\UI\Controller;

use App\Application\JwtValidator\JwtValidatorInterface;
use App\Application\UseCase\MediaFile\CreateMediaFileHandler;
use App\Application\UseCase\MediaFile\CreateMediaFileRequest;
use App\Application\UseCase\MediaFile\ListLinkHandler;
use App\Application\UseCase\MediaFile\ListLinkRequest;
use App\Application\UseCase\MediaFile\ListMediaFileHandler;
use App\Application\UseCase\MediaFile\ListMediaFileRequest;
use App\Domain\Enum\ApiStatus;
use App\Domain\Exception\DataValidationException;
use App\Domain\Exception\GetDataException;
use App\Domain\Exception\JwtValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use OpenApi\Attributes as OA;

#[OA\Tag('media-file')]
#[Route('api/v1/media-file/')]
class MediaFileController extends AbstractController
{
    public function __construct(
        private readonly CreateMediaFileHandler $createMediaFileHandler,
        private readonly ListMediaFileHandler $listMediaFileHandler,
        private readonly TranslatorInterface $translator
    ) {}

    #[OA\Get(
        path: '/api/v1/media-file/list',
        summary: 'List media files',
        description: 'Returns a paginated list of media files stored in the system.',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', description: 'Number of items per page', required: false, schema: new OA\Schema(type: 'integer', default: 5)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated media file list', content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'success'),
                    new OA\Property(property: 'results', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'pagination', type: 'object', properties: [
                        new OA\Property(property: 'page', type: 'integer'),
                        new OA\Property(property: 'limit', type: 'integer'),
                        new OA\Property(property: 'total', type: 'integer'),
                    ]),
                ]
            )),
            new OA\Response(response: 400, description: 'Invalid request parameters'),
            new OA\Response(response: 500, description: 'Server error'),
        ]
    )]
    #[Route('list', name: 'api.v1.mediafile.list', methods: ['GET'])]
    public function listMediaFiles(Request $request): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 5);
            $dto = new ListMediaFileRequest($page, $limit);
            $results = $this->listMediaFileHandler->handle($dto);
        } catch (GetDataException $getDataException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $getDataException->getMessage()
            ]);
        }

        return new JsonResponse([
            'status' => ApiStatus::SUCCESS,
            'results' => $results['items'],
            'pagination' => $results['pagination']
        ], Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/v1/media-file/create',
        summary: 'Create a media file',
        description: 'Creates a new media file record. Requires a Bearer token in the Authorization header.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'server', type: 'string'),
                    new OA\Property(property: 'quality', type: 'string'),
                    new OA\Property(property: 'language', type: 'string', nullable: true),
                    new OA\Property(property: 'link', type: 'string', nullable: true),
                    new OA\Property(property: 'iframe', type: 'string', nullable: true),
                    new OA\Property(property: 'movie', type: 'string'),
                    new OA\Property(property: 'movieImage', type: 'string'),
                    new OA\Property(property: 'movieTitle', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Media file created successfully'),
            new OA\Response(response: 400, description: 'Validation failed or missing fields'),
            new OA\Response(response: 401, description: 'Missing or invalid authorization token'),
        ]
    )]
    #[Route('create', name: 'api.v1.mediafile.create', methods: ['POST'])]
    public function createMediaFile(Request $request, JwtValidatorInterface $jwtValidator): JsonResponse
    {
        try {
            $payload = $request->toArray();

            $authHeader = $request->headers->get('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return new JsonResponse([
                    'status' => 'Error',
                    'message' => 'Missing or invalid Authorization header'
                ], Response::HTTP_UNAUTHORIZED);
            }

            /* Remove the first part and extract the key */
            $token = substr($authHeader, 7);

            /* Validate Token */
            try {
                $user = $jwtValidator->validate($token);
            } catch (JwtValidationException $jwtValidation) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $jwtValidation->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            $dto = new CreateMediaFileRequest(
                $payload['server'] ?? '',
                $payload['quality'] ?? '',
                $payload['language'] ?? null,
                $payload['link'] ?? null,
                $payload['iframe'] ?? null,
                $payload['movie'] ?? '',
                $payload['movieImage'] ?? '',
                $payload['movieTitle'] ?? '',
                $user
            );

            $this->createMediaFileHandler->handle($dto);
        } catch (DataValidationException $e) {
            $translatedErrors = [];

            foreach ($e->getErrors() as $field => $messageKey) {
                $translatedErrors[$field] = $this->translator->trans($messageKey);
            }

            return new JsonResponse([
                'status' => ApiStatus::ERROR,
                'errors' => $translatedErrors,
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'status' => ApiStatus::SUCCESS,
            'message' => $this->translator->trans('api.response.created', ['%element%' => 'media file']),
        ], Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/v1/media-file/link',
        summary: 'Retrieve a media file link',
        description: 'Fetches a media file link by its id. Returns 204 when no link is found.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'string')
                ],
                required: ['id']
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Link found', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 204, description: 'No link found'),
            new OA\Response(response: 400, description: 'Missing or invalid id'),
        ]
    )]
    #[Route('link', name: 'api.get-link', methods: ['GET'])]
    public function getLink(Request $request, ListLinkHandler $handler): JsonResponse
    {
        $payload = $request->toArray();
        $dto = new ListLinkRequest($payload['id'] ?? '');
        $link = $handler->handle($dto);
        if (is_null($link)) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }
        //crear request y handler
        return new JsonResponse($link, 200);
    }
}
