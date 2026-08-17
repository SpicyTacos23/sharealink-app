<?php

namespace App\Controller;

use App\Interfaces\AvatarProviderInterface;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\RemoteUserRepositoryInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/avatar/')]
final class AvatarController extends SettingsController
{

    #[Route('list', name: 'app.settings.get-avatars', methods: ['GET'])]
    public function getAvatars(AvatarProviderInterface $avatarProvider): Response
    {
        return $this->render('Partials/_avatar_list.html.twig', [
            'avatars' => $avatarProvider->getAllAvatars($this->getParameter('kernel.project_dir'))
        ]);
    }

    #[Route('change', name: 'app.settings.change-avatar', methods: ['POST'])]
    public function changeAvatar(Request $request, RemoteUserRepositoryInterface $userRepository, JwtValidatorInterface $jwt): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $avatar = $data['avatar'] ?? null;

        if (!$avatar) {
            return new JsonResponse(['error' => 'No avatar provided'], 400);
        }

        $userPayload = $jwt->getValidTokenPayload();
        try {
            $userRepository->updateUserAvatar(basename($avatar), $jwt->getToken());
            $this->addFlash('success', "settings.avatar.change-success");
        } catch (ErrorException $errEx) {
            $this->addFlash('danger', $errEx->getMessage());
        }

        return new JsonResponse(['success' => true]);
    }
}
