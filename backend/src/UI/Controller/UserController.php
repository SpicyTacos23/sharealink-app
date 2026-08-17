<?php

namespace App\UI\Controller;

use App\Application\JwtValidator\JwtValidatorInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag('user')]
#[Route('api/v1/user/')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    #[OA\Post(
        path: '/api/v1/user/get-user',
        summary: 'Get authenticated user',
        description: 'Returns user data for the authenticated JWT bearer token.',
        parameters: [
            new OA\Parameter(name: 'Authorization', in: 'header', description: 'Bearer JWT token', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user data', content: new OA\JsonContent(type: 'object', properties: [
                new OA\Property(property: 'uuid', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'username', type: 'string'),
                new OA\Property(property: 'avatar', type: 'string', nullable: true),
            ])),
            new OA\Response(response: 401, description: 'Invalid or missing token'),
        ]
    )]
    #[Route('get-user', methods: ['GET'])]
    public function getUserApi(Request $request, JwtValidatorInterface $jwtValidator): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        if (is_null($token)) {
            return new JsonResponse(['error' => 'Authentication Header not found!'], Response::HTTP_FORBIDDEN);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $jwtValidator->validate($token);

        return new JsonResponse([
            'uuid' => $user->getUuid(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'avatar' => $user->getAvatar()
        ], Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/v1/user/update-avatar',
        summary: 'Update user avatar',
        description: 'Updates the authenticated user avatar URL.',
        parameters: [
            new OA\Parameter(name: 'Authorization', in: 'header', description: 'Bearer JWT token', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(type: 'object', properties: [
                new OA\Property(property: 'newAvatar', type: 'string'),
            ], required: ['newAvatar'])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Avatar updated'),
            new OA\Response(response: 400, description: 'Missing or invalid avatar'),
            new OA\Response(response: 401, description: 'Invalid or missing token'),
        ]
    )]
    #[Route('update-avatar', methods: ['PUT'])]
    public function updateUserAvatar(Request $request, JwtValidatorInterface $jwtValidator): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        if (is_null($token)) {
            return new JsonResponse(['error' => 'Authentication Header not found!'], Response::HTTP_FORBIDDEN);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $jwtValidator->validate($token);

        $data = $request->toArray();
        if (!isset($data['newAvatar'])) {
            return new JsonResponse("Missing new avatar!", Response::HTTP_BAD_REQUEST);
        }

        $authenticatedUserUuid = $user->getUuid();

        try {
            $this->userRepository->updateAvatar($authenticatedUserUuid, $data['newAvatar']);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/v1/user/update-username',
        summary: 'Update user username',
        description: 'Updates the username for the authenticated user.',
        parameters: [
            new OA\Parameter(name: 'Authorization', in: 'header', description: 'Bearer JWT token', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(type: 'object', properties: [
                new OA\Property(property: 'newUsername', type: 'string'),
            ], required: ['newUsername'])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Username updated'),
            new OA\Response(response: 400, description: 'Missing or invalid username'),
            new OA\Response(response: 401, description: 'Invalid or missing token'),
        ]
    )]
    #[Route('update-username', methods: ['PUT'])]
    public function updateUserApiUsername(Request $request, JwtValidatorInterface $jwtValidator): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        if (is_null($token)) {
            return new JsonResponse(['error' => 'Authentication Header not found!'], Response::HTTP_FORBIDDEN);
        }
        $token = str_replace('Bearer ', '', $token);
        $user = $jwtValidator->validate($token);

        $data = $request->toArray();
        if (empty($data)) {
            return new JsonResponse("Empty data received!", Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['newUsername'])) {
            return new JsonResponse("Missing new username!", Response::HTTP_BAD_REQUEST);
        }

        $authenticatedUserUuid = $user->getUuid();

        try {
            $this->userRepository->updateUsername($authenticatedUserUuid, $data['newUsername']);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([], Response::HTTP_OK);
    }
}
