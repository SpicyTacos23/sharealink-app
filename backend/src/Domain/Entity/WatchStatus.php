<?php

namespace App\Domain\Entity;

use App\Shared\Traits\dateTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WatchStatus
{
    use dateTrait;
    
    /** @var int|null */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'watchStatuses')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'watchStatuses')]
    private ?Movie $movieId = null;

    #[ORM\ManyToOne(inversedBy: 'watchStatuses')]
    private ?Show $showId = null;

    #[ORM\ManyToOne(inversedBy: 'watchStatuses')]
    private ?Episode $episodeId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getMovieId(): ?Movie
    {
        return $this->movieId;
    }

    public function setMovieId(?Movie $movieId): static
    {
        $this->movieId = $movieId;

        return $this;
    }

    public function getShowId(): ?Show
    {
        return $this->showId;
    }

    public function setShowId(?Show $showId): static
    {
        $this->showId = $showId;

        return $this;
    }

    public function getEpisodeId(): ?Episode
    {
        return $this->episodeId;
    }

    public function setEpisodeId(?Episode $episodeId): static
    {
        $this->episodeId = $episodeId;

        return $this;
    }
}
