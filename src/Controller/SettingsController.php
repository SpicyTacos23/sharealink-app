<?php

namespace App\Controller;

use App\Attribute\RequireLogin;
use App\Interfaces\AvatarProviderInterface;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\RemoteUserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
#[RequireLogin]
class SettingsController extends AbstractController
{
    public function __construct(
    ) {}

    #[Route('', name: 'app.settings')]
    public function index(RemoteUserRepositoryInterface $userRepository, JwtValidatorInterface $jwtValidator, AvatarProviderInterface $avatarProvider)
    {
        //GET user profile image
        $avatar = $userRepository->getUserAvatar($jwtValidator->getToken());
        if (empty($avatar)) {
            $avatars = $avatarProvider->getAllAvatars($this->getParameter('kernel.project_dir'));
            $avatar = basename($avatars[rand(0, count($avatars) - 1)]);
        }

        $username = $userRepository->getUserUsername($jwtValidator->getToken());
        return $this->render('Settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'title' => 'Settings',
            'username' => "@$username",
            'avatar' => "/build/images/avatars/" . $avatar
        ]);
    }
}
