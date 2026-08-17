<?php

namespace App\Application\UseCase\Links;

use Symfony\Component\Validator\Constraints as Assert;

class ListShowLinksRequest
{
    #[Assert\NotBlank(message: 'api.show.list-links.name')]
    public string $id;

    #[Assert\NotBlank(message: 'api.show.list-links.name')]
    #[Assert\GreaterThan(0, message: 'api.show.list-links.season')]
    public string $season;

    #[Assert\NotBlank(message: 'api.show.list-links.name')]
    #[Assert\GreaterThan(0, message: 'api.show.list-links.episode')]
    public string $episode;

    public function __construct(string $id, string $season, string $episode)
    {
        $this->id = $id;
        $this->season = $season;
        $this->episode = $episode;
    }
}
