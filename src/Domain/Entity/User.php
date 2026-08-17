<?php

namespace App\Domain\Entity;

use App\Shared\Traits\dateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
#[ORM\Table]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use dateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private string $uuid;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    /**
     * @var non-empty-string
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private string $email;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private ?string $username = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private ?string $avatar = null;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: 'uploadedBy')]
    private Collection $uploadedMovies;

    /**
     * @var Collection<int, WatchStatus>
     */
    #[ORM\OneToMany(targetEntity: WatchStatus::class, mappedBy: 'userId')]
    private Collection $watchStatuses;

    /**
     * @var Collection<int, MediaFile>
     */
    #[ORM\OneToMany(targetEntity: MediaFile::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $mediaFiles;


    /** @param non-empty-string $email */
    public function __construct(string $username, string $email, string $password)
    {
        $this->uploadedMovies = new ArrayCollection();
        $this->watchStatuses = new ArrayCollection();
        $this->initDates();
        $this->mediaFiles = new ArrayCollection();
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @return non-empty-string
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param non-empty-string $email
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getUploadedMovies(): Collection
    {
        return $this->uploadedMovies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->uploadedMovies->contains($movie)) {
            $this->uploadedMovies->add($movie);
            $movie->setUploadedBy($this);
        }

        return $this;
    }

    public function removeUploadedMovie(Movie $movie): static
    {
        if ($this->uploadedMovies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getUploadedBy() === $this) {
                $movie->setUploadedBy(null);
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
            $watchStatus->setUserId($this);
        }

        return $this;
    }

    public function removeWatchStatus(WatchStatus $watchStatus): static
    {
        if ($this->watchStatuses->removeElement($watchStatus)) {
            // set the owning side to null (unless already changed)
            if ($watchStatus->getUserId() === $this) {
                $watchStatus->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaFile>
     */
    public function getMediaFiles(): Collection
    {
        return $this->mediaFiles;
    }

    public function addMediaFile(MediaFile $mediaFile): static
    {
        if (!$this->mediaFiles->contains($mediaFile)) {
            $this->mediaFiles->add($mediaFile);
            $mediaFile->setUser($this);
        }

        return $this;
    }

    public function removeMediaFile(MediaFile $mediaFile): static
    {
        if ($this->mediaFiles->removeElement($mediaFile)) {
            // set the owning side to null (unless already changed)
            if ($mediaFile->getUser() === $this) {
                $mediaFile->setUser(null);
            }
        }

        return $this;
    }
}
