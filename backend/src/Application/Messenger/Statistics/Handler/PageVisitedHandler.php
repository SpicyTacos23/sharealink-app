<?php

namespace App\Application\Messenger\Statistics\Handler;

use App\Application\Messenger\Statistics\Message\PageVisitedMessage;
use App\Domain\Entity\PageVisit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PageVisitedHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(PageVisitedMessage $message): string
    {
        $visit = new PageVisit(
            $message->path,
            $message->visitedAt,
            $message->ip,
            $message->userAgent,
            $message->referer,
        );

        $this->em->persist($visit);
        $this->em->flush();

        return $message->path;
    }
}
