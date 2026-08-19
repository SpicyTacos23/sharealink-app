<?php

namespace App\Controller;

use App\Attribute\RequireLogin;
use App\Controller\SettingsController;
use App\Form\ChangePasswordType;
use App\Form\DeleteAccountType;
use App\Form\UsernameType;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\UserApiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/')]
#[RequireLogin]
class ApiUserController extends SettingsController
{
    #[Route('user-profile', name: 'app.settings.profile', methods: ['GET'])]
    public function loadUserProfile(JwtValidatorInterface $jwtValidator): Response
    {
        $usernameForm = $this->createForm(UsernameType::class, null, [
            'action' => $this->generateUrl('app.settings.update_username')
        ]);

        $passwordForm = $this->createForm(ChangePasswordType::class, null, [
            'action' => $this->generateUrl('app.settings.update_password')
        ]);

        $deleteForm = $this->createForm(DeleteAccountType::class, null, [
            'action' => $this->generateUrl('app.settings.delete_account')
        ]);

        return $this->render('Settings/user_profile.html.twig', [
            'title' => 'User Profile',
            'usernameForm' => $usernameForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'deleteForm' => $deleteForm->createView(),
            'coins' => 50
        ]);
    }

    #[Route('update-username', name: 'app.settings.update_username', methods: ['POST'])]
    public function updateUsername(
        Request $request,
        JwtValidatorInterface $jwtValidator,
        UserApiInterface $userApi
    ): Response {
        $userPayload = $jwtValidator->getValidTokenPayload();

        $form = $this->createForm(UsernameType::class, null, [
            'action' => $this->generateUrl('app.settings.update_username')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($userPayload) || is_null($userPayload['email']) || is_null($jwtValidator->getToken())) {
                $this->addFlash('warning', 'Credentials expired, please log in again.');
                return $this->redirectToRoute('app.login');
            }
            $userApi->updateUsername(
                $userPayload['email'],
                $form->get('username')->getData(),
                $jwtValidator->getToken()
            );

            $this->addFlash('success', 'Username updated successfully!');
        }

        return $this->redirectToRoute('app.settings');
    }

    #[Route('update-password', name: 'app.settings.update_password', methods: ['POST'])]
    public function updatePassword(
        Request $request,
        JwtValidatorInterface $jwtValidator,
        UserApiInterface $userApi
    ): Response {
        $userPayload = $jwtValidator->getValidTokenPayload();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($userPayload) || is_null($userPayload['email']) || is_null($jwtValidator->getToken())) {
                $this->addFlash('warning', 'Credentials expired, please log in again.');
                return $this->redirectToRoute('app.login');
            }
            $userApi->updatePassword(
                $userPayload['email'],
                $form->get('newPassword')->getData(),
                $jwtValidator->getToken()
            );

            $this->addFlash('success', 'Password updated successfully!');
        }

        return $this->redirectToRoute('app.settings.profile');
    }

    #[Route('delete-account', name: 'app.settings.delete_account', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        JwtValidatorInterface $jwtValidator,
        UserApiInterface $userApi
    ): Response {
        $userPayload = $jwtValidator->getValidTokenPayload();

        $form = $this->createForm(DeleteAccountType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*$userApi->deleteAccount(
                $userPayload['email'],
                $jwtValidator->getToken()
            ); */

            $this->addFlash('success', 'Account deleted successfully!');
        }

        return $this->redirectToRoute('app.settings.profile');
    }
}
