<?php

namespace App\Application\UseCase\MediaFile;

use App\Domain\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMediaFileRequest
{
    #[Assert\NotBlank(message: 'media_file.path.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'media_file.path.max_length')]
    public string $server;

    #[Assert\NotBlank(message: 'media_file.quality.not_blank')]
    #[Assert\GreaterThan(0)]
    public int $quality;

    #[Assert\Length(max: 10, maxMessage: 'media_file.language.max_length')]
    public string $language;

    #[Assert\NotBlank(message: 'media_file.link.not_blank')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'media_file.link.max_length',
        min: 3,
        minMessage: 'media_file.link.min_message'
    )]
    public string $link;

    #[Assert\Length(
        max: 255,
        maxMessage: 'media_file.iframe.max_length'
    )]
    public string $iframe = '';

    #[Assert\NotBlank(message: 'media_file.movie.not_blank')]
    public string $movie;

    #[Assert\NotBlank(message: 'media_file.movieImage.not_blank')]
    public string $movieImage;

    #[Assert\NotBlank(message: 'media_file.movieTitle.not_blank')]
    public string $movieTitle;

    #[Assert\NotNull(message: 'media_file.user.null')]
    public User $user;

    public function __construct(
        string $server,
        string $quality,
        string $language,
        string $link,
        string $iframe,
        string $movie,
        string $movieImage,
        string $movieTitle,
        User $user
    ) {
        $this->server = $server;
        $this->quality = (int) $quality;
        $this->language = $language;
        $this->link = $link;
        $this->iframe = $iframe;
        $this->movie = $movie;
        $this->movieImage = $movieImage;
        $this->movieTitle = $movieTitle;
        $this->user = $user;
    }
}
