<?php

namespace App\Controller;

use App\Interfaces\JwtValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{

    public function __construct(
        private JwtValidatorInterface $jwtEncoder
    ) {}

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        $response = new Response();

        $this->jwtEncoder->removeAuthToken($response);

        return $response;
    }
}
