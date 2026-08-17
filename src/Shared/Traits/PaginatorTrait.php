<?php

namespace App\Shared\Traits;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginatorTrait
{
    #[Assert\NotBlank(message: 'api.genre.list.error.page')]
    public int $page;

    #[Assert\NotBlank(message: 'api.genre.list.error.limit')]
    public int $limit;

    public function __construct(int $page, int $limit)
    {
        $this->initFields($page, $limit);
    }

    public function initFields(int $page, int $limit): void
    {
        $this->page = $page;
        $this->limit = $limit;
    }
}
