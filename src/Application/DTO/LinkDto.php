<?php

namespace App\Application\DTO;

use App\Application\DTO\TMDB\MovieSummaryDto;
use App\Domain\Enum\MediaType;

final class LinkDto
{
    public function __construct(
        public int $id,
        public string $server,
        public int $quality,
        public UserDto $user,
        public string $link,
        public string $iframelink,
        public MediaType $mediaType,
        public MovieSummaryDto $movie,
        public string $language,
    ) {}
}
