<?php

namespace App\Domain\Entity;

use App\Shared\Traits\dateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class Movie
{
    use dateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * ID IMDB (tt1234567)
     */
    #[ORM\Column(nullable: true)]
    private ?string $movieId = null;

    /**
     * ID TMDB (numérico)
     */
    #[ORM\Column(nullable: true)]
    private ?int $tmdbId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $overview = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posterPath = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $movieImage = null;

    /**
     * @var Collection<int, MediaFile>
     */
    #[ORM\OneToMany(targetEntity: MediaFile::class, mappedBy: 'movie')]
    private Collection $mediaFileId;

    #[ORM\ManyToOne(inversedBy: 'uploadedMovies')]
    private ?User $uploadedBy = null;

    /**
     * @var Collection<int, WatchStatus>
     */
    #[ORM\OneToMany(targetEntity: WatchStatus::class, mappedBy: 'movieId')]
    private Collection $watchStatuses;

    public function __construct()
    {
        $this->mediaFileId = new ArrayCollection();
        $this->watchStatuses = new ArrayCollection();
        $this->initDates();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovieId(): ?string
    {
        return $this->movieId;
    }

    public function setMovieId(?string $movieId): static
    {
        $this->movieId = $movieId;
        return $this;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): static
    {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(?string $overview): static
    {
        $this->overview = $overview;
        return $this;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    public function setPosterPath(?string $posterPath): static
    {
        $this->posterPath = $posterPath;
        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    public function getMovieImage(): ?string
    {
        return $this->movieImage;
    }

    public function setMovieImage(?string $movieImage): static
    {
        $this->movieImage = $movieImage;
        return $this;
    }

    /**
     * @return Collection<int, MediaFile>
     */
    public function getMediaFileId(): Collection
    {
        return $this->mediaFileId;
    }

    public function addMediaFileId(MediaFile $mediaFileId): static
    {
        if (!$this->mediaFileId->contains($mediaFileId)) {
            $this->mediaFileId->add($mediaFileId);
            $mediaFileId->setMovie($this);
        }
        return $this;
    }

    public function removeMediaFileId(MediaFile $mediaFileId): static
    {
        if ($this->mediaFileId->removeElement($mediaFileId)) {
            if ($mediaFileId->getMovie() === $this) {
                $mediaFileId->setMovie(null);
            }
        }
        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): static
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
    }

    /**
     * @return Collection<int, WatchStatus>
     */
    public function getWatchStatus(): Collection
    {
        return $this->watchStatuses;
    }

    /**
     * @param array<mixed> $raw
     */
    public static function createFromTmdbPayload(array $raw): self
    {
        $movie = new self();

        // TMDB ID
        $movie->setMovieId(isset($raw['id']) ? $raw['id'] : null);

        // IMDB ID (solo en details)
        $movie->setTmdbId($raw['imdb_id'] ?? null);

        // Title
        $movie->setTitle($raw['title'] ?? $raw['original_title'] ?? null);

        // Overview
        $movie->setOverview($raw['overview'] ?? null);

        // Poster
        $movie->setPosterPath($raw['poster_path'] ?? null);

        // Backdrop
        $movie->setMovieImage($raw['backdrop_path'] ?? null);

        // Release date
        if (!empty($raw['release_date'])) {
            try {
                $movie->setReleaseDate(new \DateTime($raw['release_date']));
            } catch (\Exception $e) {
                $movie->setReleaseDate(null);
            }
        }

        return $movie;
    }
}
