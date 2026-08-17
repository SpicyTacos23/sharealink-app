<?php

namespace App\Application\UseCase\MediaFile;

use Symfony\Component\Validator\Constraints as Assert;

class ListLinkRequest
{
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    public int $id;

    public function __construct(string $id)
    {
        $this->id = (int)$id;
    }
}
