<?php
namespace App\UI\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/api/v1/auth/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Este controlador normalmente no se ejecutará si json_login intercepta correctamente.
        return new JsonResponse(['message' => 'If you see this, json_login did not intercept.'], 400);
    }
}
