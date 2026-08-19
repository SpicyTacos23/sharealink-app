<?php

namespace App\Controller;

use App\Exception\LoginErrorException;
use App\Form\LoginFormType;
use App\Interfaces\RemoteLoginApiInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app.login')]
    public function login(Request $request, RemoteLoginApiInterface $remoteLogin, SessionInterface $session, TranslatorInterface $translator): Response
    {
        // Guardar la URL desde la que vino el usuario
        $referer = $request->headers->get('referer');
        if ($referer) {
            $session->set('login_redirect', $referer);
        }

        $form = $this->createForm(LoginFormType::class, null, ['action' => $this->generateUrl('app.login')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            //@TODO: Add some validation here!
            if (is_null($data['email']) || is_null($data['password'])) {
                throw new Exception("ERROR!");
            }

            //@Use another cookie to store last url to go back. 
            try {
                $cookie = $remoteLogin->getToken($data);
            } catch (LoginErrorException $loginErrorEx) {
                //@TODO: Does not work (?) Redirect loses flash?
                $this->addFlash('error', $translator->trans('app.login.error'));
                return $this->redirectToRoute('app.login');
            }

            $url = $this->generateUrl('app.homepage');
            if ($request->query->getBoolean('partial')) {
                $url = $referer;
            } 

            if (!is_string($url)) {
            throw new \LogicException('Expected kernel.project_dir to be a string.');
        }

            $response = new RedirectResponse($url);
            $response->headers->setCookie($cookie);
            $this->addFlash('success', $translator->trans('app.login.success'));

            return $response;
        }

        // If we've been redirected from new media. Show only side login
        $template = 'Login/index.html.twig';
        if ((bool) $request->query->get('partial')) {
            $template = 'Partials/_login.html.twig';
        }

        return $this->render($template, [
            'title' => 'login',
            'loginForm' => $form->createView(),
        ])->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
