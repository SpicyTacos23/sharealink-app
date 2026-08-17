<?php

namespace App\Domain\Entity;

use App\Shared\Traits\dateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Episode
{
    use dateTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false)]
    private int $id;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $seasonNumber = null;

    #[ORM\Column]
    private ?int $episodeNumber = null;

    #[ORM\ManyToOne(inversedBy: 'episodes')]
    private ?Show $showId = null;

    #[ORM\ManyToOne(inversedBy: 'episodes')]
    private ?MediaFile $mediaFileId = null;

    /**
     * @var Collection<int, WatchStatus>
     */
    #[ORM\OneToMany(targetEntity: WatchStatus::class, mappedBy: 'episodeId')]
    private Collection $watchStatuses;

    public function __construct()
    {
        $this->watchStatuses = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSeasonNumber(): ?int
    {
        return $this->seasonNumber;
    }

    public function setSeasonNumber(int $seasonNumber): static
    {
        $this->seasonNumber = $seasonNumber;

        return $this;
    }

    public function getEpisodeNumber(): ?int
    {
        return $this->episodeNumber;
    }

    public function setEpisodeNumber(int $episodeNumber): static
    {
        $this->episodeNumber = $episodeNumber;

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

    public function getMediaFileId(): ?MediaFile
    {
        return $this->mediaFileId;
    }

    public function setMediaFileId(?MediaFile $mediaFileId): static
    {
        $this->mediaFileId = $mediaFileId;

        return $this;
    }

    /**
     * @return Collection<int, WatchStatus>
     */
    public function getWatchStatuses(): Collection
    {
        return $this->watchStatuses;
    }

    public function addWatchStatus(WatchStatus $watchStatus): static
    {
        if (!$this->watchStatuses->contains($watchStatus)) {
            $this->watchStatuses->add($watchStatus);
            $watchStatus->setEpisodeId($this);
        }

        return $this;
    }

    public function removeWatchStatus(WatchStatus $watchStatus): static
    {
        if ($this->watchStatuses->removeElement($watchStatus)) {
            // set the owning side to null (unless already changed)
            if ($watchStatus->getEpisodeId() === $this) {
                $watchStatus->setEpisodeId(null);
            }
        }

        return $this;
    }
}
