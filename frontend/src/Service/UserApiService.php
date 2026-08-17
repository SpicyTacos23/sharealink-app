<?php

namespace App\Service;

use App\Interfaces\UserApiInterface;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserApiService implements UserApiInterface
{
    const USER_URL = 'http://127.0.0.1:8000/api/v1/user/';

    public function __construct(private readonly HttpClientInterface $client) {}

    public function getUserPayload(string $authToken): JsonResponse
    {
        $url = self::USER_URL . "get-user";
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Accept'        => 'application/json',
                ]
            ]
        );
        $result = $response->getContent(false);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($result, true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function updatePassword(string $email, string $newPassword, string $authToken): JsonResponse
    {
        $url = self::USER_URL . "update-password";
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'newPassword' => $newPassword
                ]
            ]
        );
        $result = $response->getContent();

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($result, true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function updateAvatar(string $avatar, string $authToken): JsonResponse
    {
        $url = self::USER_URL . "update-avatar";
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'newAvatar' => $avatar
                ]
            ]
        );
        $result = $response->getContent(false);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($result, true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }

    public function updateUsername(string $email, string $newUsername, string $authToken): JsonResponse
    {
        $url = self::USER_URL . "update-username";
        $response = $this->client->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'newUsername' => $newUsername
                ]
            ]
        );
        $result = $response->getContent(false);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return new JsonResponse(json_decode($result, true));
        }

        return new JsonResponse("An error occurred while trying to contact server", $response->getStatusCode());
    }
    
}
