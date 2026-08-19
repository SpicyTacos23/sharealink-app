<?php

namespace App\Repository;

use App\Interfaces\RemoteUserRepositoryInterface;
use App\Interfaces\UserApiInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\Response;

final class RemoteUserRepository implements RemoteUserRepositoryInterface
{
    /** @var array<string, mixed> */
    private array $userData = [];

    public function __construct(private readonly UserApiInterface $userApi) {}

    private function getUserData(string $authToken): void
    {
        $user = $this->userApi->getUserPayload($authToken);
        $content = $user->getContent();

        if ($content === false) {
            $this->userData = [];
            return;
        }

        $decoded = json_decode($content, true);
        $this->userData = is_array($decoded) ? $decoded : [];
    }

    public function getUserAvatar(string $authToken): string
    {
        if (empty($this->userData)) {
            $this->getUserData($authToken);
        }

        return $this->userData['avatar'] ?? '';
    }

    public function getUserUsername(string $authToken): string
    {
        if (empty($this->userData)) {
            $this->getUserData($authToken);
        }

        return $this->userData['username'] ?? '';
    }

    public function updateUserAvatar(string $avatar, string $authToken): void
    {
        $response = $this->userApi->updateAvatar($avatar, $authToken);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new ErrorException("Error updating avatar!");
        }
        return;
    }
}