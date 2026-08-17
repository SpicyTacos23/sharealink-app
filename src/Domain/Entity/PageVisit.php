<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'page_visit')]
class PageVisit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 512)]
    private string $path;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $visitedAt;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $referer = null;

    public function __construct(
        string $path,
        \DateTimeImmutable $visitedAt,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $referer = null,
    ) {
        $this->path = $path;
        $this->visitedAt = $visitedAt;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->referer = $referer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getVisitedAt(): \DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }
    
}
