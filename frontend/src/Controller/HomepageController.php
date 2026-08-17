<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomepageController extends AbstractController
{
    #[Route('', name: 'app.homepage')]
    public function index(): Response
    {
        return $this->render('Homepage/homepage.html.twig', [
            'title' => 'Share @ Link- Home'
        ]);
    }
}
