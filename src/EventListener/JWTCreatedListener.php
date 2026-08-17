<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Domain\Entity\User;

/**
 * @author ssole:
 * Created to override default JWT info "username" and use email instead
 */
class JWTCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $payload = $event->getData();

        // Sobrescribimos la claim 'username' con el email real del usuario
        $payload['username'] = $user->getEmail();

        // (Opcional) añadimos también la claim 'email' por claridad
        $payload['email'] = $user->getEmail();

        $event->setData($payload);
    }
}
