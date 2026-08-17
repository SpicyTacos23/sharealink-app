<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MediaType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MediaFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $server = null;

    #[ORM\Column]
    private ?int $quality = null;

    #[ORM\ManyToOne(inversedBy: 'mediaFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iframeLink = null;

    #[ORM\Column(length: 25)]
    private ?MediaType $mediaType = null;

    #[ORM\ManyToOne(inversedBy: 'mediaFileId')]
    #[ORM\JoinColumn(name: 'mediaId', referencedColumnName: 'id', nullable: false)]
    private ?Movie $movie = null;

    /**
     * @var Collection<int, Episode>
     */
    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: 'mediaFileId')]
    private Collection $episodes;

    #[ORM\Column(length: 10)]
    private ?string $language = null;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(string $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $userId): static
    {
        $this->user = $userId;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getIframeLink(): ?string
    {
        return $this->iframeLink;
    }

    public function setIframeLink(?string $iframeLink): static
    {
        $this->iframeLink = $iframeLink;

        return $this;
    }

    public function getMediaType(): ?MediaType
    {
        return $this->mediaType;
    }

    public function setMediaType(MediaType $mediaType): static
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }
}
