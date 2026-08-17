<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.api_error')]
        private LoggerInterface $apiErrorLogger // canal api_error
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // Solo logueamos errores de rutas API
        if (!str_starts_with($request->getPathInfo(), '/api/v1/imdb-dev/')) {
            return;
        }

        $exception = $event->getThrowable();

        $this->apiErrorLogger->error('API Exception', [
            'message' => $exception->getMessage(),
            'type' => $exception::class,
            'path' => $request->getPathInfo(),
            'query' => $request->query->all(),
            'body' => $request->request->all(),
        ]);
    }
}
