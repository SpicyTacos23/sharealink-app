<?php

namespace App\Service;

use App\Interfaces\JwtValidatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Response;

class JwtValidator implements JwtValidatorInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private JWTEncoderInterface $jwtEncoder
    ) {}

    public function getValidTokenPayload(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        $token = $request->cookies->get('userAuthToken');

        if (!$token) {
            return null; // No hay cookie → no está logueado
        }

        try {
            $payload = $this->jwtEncoder->decode($token);
            return $payload; // Token válido
        } catch (\Exception $e) {
            return null; // Token inválido o expirado
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->getValidTokenPayload() !== null;
    }

    public function getToken(): ?string
    {
        return $this->requestStack->getCurrentRequest()->cookies->get('userAuthToken');
    }

    public function removeAuthToken(Response $response): void
    {
        $response->headers->clearCookie('userAuthToken');
    }
}
