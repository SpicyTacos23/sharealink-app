<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowDetails;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadShowDetailsRequest
{
    #[Assert\NotBlank(message: 'api.tmdb.show.details.id')]
    #[Assert\NotNull(message: 'api.tmdb.show.details.id')]
    #[Assert\Length(max: 15, min: 1)]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
