<?php

namespace App\EventSubscriber;

use App\Application\Messenger\Statistics\Message\PageVisitedMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zenstruck\Messenger\Monitor\Stamp\TagStamp;

final class PageVisitedSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Excluir API: /api/v1/imdb-dev/*
        /* if (str_starts_with($request->getPathInfo(), '/api/v1/imdb-dev')) {
            return;
        }*/

        // Excluir peticiones async (AJAX)
        if ($request->isXmlHttpRequest()) {
            return;
        }

        //Exclude admin control
        if (str_starts_with($request->getPathInfo(), '/admin/')) {
            return;
        }

        // Exclude links from movie because movie is already called
        if (
            str_starts_with($request->getPathInfo(), '/api/v1/movie/links') ||
            str_starts_with($request->getPathInfo(), '/api/v1/show/links')
        ) {
            return;
        }

        //Exclude seasons & episodes
        if (
            str_starts_with($request->getPathInfo(), '/api/v1/imdb-dev/show/seasons') ||
            str_starts_with($request->getPathInfo(), '/api/v1/imdb-dev/show/episodes')
        ) {
            return;
        }

        /* @TODO: Is there a better way to do this? seems excessive */
        return;
        /*
        $message = new PageVisitedMessage(
            path: $request->getPathInfo(),
            visitedAt: new \DateTimeImmutable(),
            ip: $request->getClientIp(),
            userAgent: $request->headers->get('User-Agent'),
            referer: $request->headers->get('Referer'),
        );

        $this->messageBus->dispatch($message, [
            new TagStamp('statistics')
        ]);
        */
    }
}
