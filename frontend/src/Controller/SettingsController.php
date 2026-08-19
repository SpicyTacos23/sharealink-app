<?php

namespace App\Controller;

use App\Attribute\RequireLogin;
use App\Interfaces\AvatarProviderInterface;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\RemoteUserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
#[RequireLogin]
class SettingsController extends AbstractController
{
    public function __construct() {}

    #[Route('', name: 'app.settings')]
    public function index(RemoteUserRepositoryInterface $userRepository, JwtValidatorInterface $jwtValidator, AvatarProviderInterface $avatarProvider): Response
    {
        //GET user profile image
        $avatar = $userRepository->getUserAvatar($jwtValidator->getToken() ?? '');
        if (empty($avatar)) {
            $projectDir = $this->getParameter('kernel.project_dir');
            if (!is_string($projectDir)) {
                throw new \LogicException('Expected kernel.project_dir to be a string.');
            }

            $avatars = $avatarProvider->getAllAvatars($projectDir);
            $avatar = basename($avatars[rand(0, count($avatars) - 1)]);
        }

        $username = $userRepository->getUserUsername($jwtValidator->getToken() ?? '');
        return $this->render('Settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'title' => 'Settings',
            'username' => "@$username",
            'avatar' => "/build/images/avatars/" . $avatar
        ]);
    }
}
