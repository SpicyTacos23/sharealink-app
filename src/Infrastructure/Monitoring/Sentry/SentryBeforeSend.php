<?php

namespace App\Infrastructure\Monitoring\Sentry;

use RuntimeException;
use Sentry\Event;
use Sentry\EventHint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SentryBeforeSend
{
    public function __invoke(Event $event, ?EventHint $hint): ?Event
    {
        $exception = $hint?->exception;

        //Manual exception doctrine_fixtures without defined configuration skipper from Sentry
        if (
            $exception instanceof \LogicException &&
            str_contains(
                $exception->getMessage(),
                'The extension with alias "doctrine_fixtures" does not have its getConfiguration() method setup.'
            )
        ) {
            return null;
        }

        //Remove favicon exception
        if (
            $exception instanceof NotFoundHttpException &&
            str_contains($exception->getMessage(), 'favicon.ico')
        ) {
            return null;
        }

        //Remove console exception in dev
        if (
            $exception instanceof RuntimeException &&
            str_contains($exception->getMessage(), '--show-private')
        ) {
            return null;
        }

        return $event;
    }
}
