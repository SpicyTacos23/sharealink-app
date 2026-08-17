<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app.error')]
    public function show(Request $request): Response
    {
        $message = $request->query->get('message', 'Ha ocurrido un error inesperado');

        return $this->render('Error/generic.html.twig', [
            'message' => $message
        ]);
    }
}
