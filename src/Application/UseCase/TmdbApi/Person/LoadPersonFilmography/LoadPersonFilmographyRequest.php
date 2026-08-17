<?php

namespace App\Application\UseCase\TmdbApi\Person\LoadPersonFilmography;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadPersonFilmographyRequest
{

    #[Assert\NotBlank(message: 'api.tmdb.person.filmography.id')]
    #[Assert\NotNull(message: 'api.tmdb.person.filmogrpahy.id')]
    #[Assert\Length(max: 15, min: 1)]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
