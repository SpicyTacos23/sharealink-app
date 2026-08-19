<?php

namespace App\EventListener;

use App\Attribute\RequireLogin;
use App\Interfaces\JwtValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequireLoginListener
{
    public function __construct(
        private JwtValidatorInterface $jwtValidator,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        [$object, $method] = $controller;

        $classAttributes = (new \ReflectionClass($object))
            ->getAttributes(RequireLogin::class);

        $methodAttributes = (new \ReflectionMethod($object, $method))
            ->getAttributes(RequireLogin::class);

        $requiresLogin = !empty($classAttributes) || !empty($methodAttributes);

        if ($requiresLogin && !$this->jwtValidator->isLoggedIn()) {
            $url = $this->urlGenerator->generate('app.login');
            $event->setController(fn() => new RedirectResponse($url));
        }
    }
}