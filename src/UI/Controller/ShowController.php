<?php

namespace App\UI\Controller;

use App\Application\UseCase\Show\ListShowLinksHandler;
use App\Application\UseCase\Show\ListShowLinksRequest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('show')]
#[Route('api/v1/show/')]
final class ShowController extends AbstractController
{
    
}
