<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Response;

interface JwtValidatorInterface
{
    public function getValidTokenPayload(): ?array;
    public function isLoggedIn(): bool;
    public function getToken(): ?string;
    public function removeAuthToken(Response $response): void;
}