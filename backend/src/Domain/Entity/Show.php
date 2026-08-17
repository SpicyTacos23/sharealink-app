<?php

namespace App\Domain\Entity;

use App\Shared\Traits\dateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`show`')]
#[ORM\HasLifecycleCallbacks]
class Show
{
    use dateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $releaseYear = null;

    /**
     * ID externo TMDB/entrada (se usa en repositorios para comparar)
     */
    #[ORM\Column(nullable: true)]
    private ?string $movieId = null;

    /**
     * Campo numérico adicional (paralelo a Movie::tmdbId)
     */
    #[ORM\Column(nullable: true)]
    private ?int $tmdbId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posterPath = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(length: 20, unique: true, nullable: true)]
    private ?string $imdbId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $showImage = null;

    /**
     * @var Collection<int, Episode>
     */
    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: 'showId')]
    private Collection $episodes;

    /**
     * @var Collection<int, WatchStatus>
     */
    #[ORM\OneToMany(targetEntity: WatchStatus::class, mappedBy: 'showId')]
    private Collection $watchStatuses;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
        $this->watchStatuses = new ArrayCollection();
        $this->initDates();
    }

    public function getId(): ?int
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?int $releaseYear): static
    {
        $this->releaseYear = $releaseYear;

        return $this;
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

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function setImdbId(?string $imdbId): static
    {
        $this->imdbId = $imdbId;
        return $this;
    }

    public function getShowImage(): ?string
    {
        return $this->showImage;
    }

    public function setShowImage(?string $showImage): static
    {
        $this->showImage = $showImage;

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): static
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setShowId($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): static
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getShowId() === $this) {
                $episode->setShowId(null);
            }
        }

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
            $watchStatus->setShowId($this);
        }

        return $this;
    }

    public function removeWatchStatus(WatchStatus $watchStatus): static
    {
        if ($this->watchStatuses->removeElement($watchStatus)) {
            // set the owning side to null (unless already changed)
            if ($watchStatus->getShowId() === $this) {
                $watchStatus->setShowId(null);
            }
        }

        return $this;
    }

    /**
     * @param array<mixed> $raw
     */
    public static function createFromImdbPayload(array $raw): self
    {
        $show = new self();
        $show->setImdbId($raw['id'])
        ->setTitle(mb_substr($raw['primaryTitle'] ?? $raw['title'] ?? '', 0, 100))
        ->setDescription(mb_substr($raw['plot'] ?? '', 0, 255))
        ->setReleaseYear($raw['startYear'] ?? null)
        ->setShowImage(isset($raw['primaryImage']) ? ($raw['primaryImage']['url'] ?? $raw['primaryImage']) : null);

        return $show;
    }

    /**
     * @param array<mixed> $raw
     */
    public static function createFromTmdbPayload(array $raw): self
    {
        $show = new self();

        // External TMDB numeric id (stored in movieId for compatibility)
        $show->setMovieId(isset($raw['id']) ? (string) $raw['id'] : null);

        // TMDB numeric id
        $show->setTmdbId(isset($raw['id']) ? (int) $raw['id'] : null);

        // Optional IMDB id (only present in detailed responses)
        if (isset($raw['imdb_id'])) {
            $show->setImdbId($raw['imdb_id']);
        }

        // Title (TV payload uses `name` / `original_name`)
        $title = mb_substr($raw['name'] ?? $raw['original_name'] ?? '', 0, 100);
        $show->setTitle($title);

        // Description / overview
        $description = mb_substr($raw['overview'] ?? '', 0, 255);
        $show->setDescription($description);


        // Release date / year from `first_air_date` if present
        $releaseYear = null;
        if (!empty($raw['first_air_date'])) {
            try {
                $dt = new \DateTime($raw['first_air_date']);
                $releaseYear = (int) $dt->format('Y');
                $show->setReleaseDate($dt);
            } catch (\Exception $e) {
                $releaseYear = null;
                $show->setReleaseDate(null);
            }
        }
        $show->setReleaseYear($releaseYear);

        // Poster and backdrop
        $show->setPosterPath($raw['poster_path'] ?? null);
        $show->setShowImage($raw['backdrop_path'] ?? $raw['poster_path'] ?? null);

        // IMDB ID is not part of the basic TV payload; leave as null

        return $show;
    }
}
