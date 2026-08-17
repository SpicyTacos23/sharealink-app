<?php

namespace App\Application\UseCase\Links;

use Symfony\Component\Validator\Constraints as Assert;

class ListMovieLinksRequest
{
    #[Assert\NotBlank(message: 'api.movie.list-links.name')]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
