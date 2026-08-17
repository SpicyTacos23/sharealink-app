<?php

namespace App\Application\UseCase\TmdbApi\Person\LoadPersonDetails;

use Symfony\Component\Validator\Constraints as Assert;

final class LoadPersonDetailsRequest
{

    #[Assert\NotBlank(message: 'api.tmdb.person.details.id')]
    #[Assert\NotNull(message: 'api.tmdb.person.details.id')]
    #[Assert\Length(max: 15, min: 1)]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
