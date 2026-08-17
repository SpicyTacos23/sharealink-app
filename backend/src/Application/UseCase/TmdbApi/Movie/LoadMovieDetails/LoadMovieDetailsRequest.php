<?php

namespace App\Application\UseCase\TmdbApi\Movie\LoadMovieDetails;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadMovieDetailsRequest
{
    #[Assert\NotBlank(message: 'api.tmdb.movie.details.id')]
    #[Assert\NotNull(message: 'api.tmdb.movie.details.id')]
    #[Assert\Length(max: 15, min: 1)]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
