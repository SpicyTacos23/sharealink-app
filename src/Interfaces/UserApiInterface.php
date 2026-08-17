<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;

interface UserApiInterface
{
    public function getUserPayload(string $authToken): JsonResponse;
    public function updatePassword(string $email, string $newPassword, string $authToken): JsonResponse;
    public function updateUsername(string $email, string $newUsername, string $authToken): JsonResponse;
    public function updateAvatar(string $avatar, string $authToken): JsonResponse;
}