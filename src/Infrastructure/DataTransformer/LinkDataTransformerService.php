<?php

namespace App\Infrastructure\DataTransformer;

use App\Application\DataTransformer\LinkDataTransformerInterface;
use App\Application\DTO\LinkDto;
use App\Application\DTO\TMDB\MovieSummaryDto;
use App\Application\DTO\UserDto;
use App\Domain\Entity\MediaFile;
use App\Domain\Enum\MediaType;

final class LinkDataTransformerService implements LinkDataTransformerInterface
{
    public function transformLink(MediaFile $media): LinkDto
    {
        $user = $media->getUser();
        $movie = $media->getMovie();

        return new LinkDto(
            id: $media->getId() ?? 0,
            server: $media->getServer() ?? '',
            quality: $media->getQuality() ?? 0,
            user: new UserDto(
                id: $user?->getId() ?? 0,
                username: $user?->getUsername() ?? ''
            ),
            link: $media->getLink() ?? '',
            iframelink: $media->getIframeLink() ?? '',
            mediaType: $media->getMediaType() ?? MediaType::UNKNOWN,
            movie: new MovieSummaryDto(
                movieId: $movie?->getId() ?? 0,
                movieName: $movie?->getTitle() ?? ''
            ),
            language: $media->getLanguage() ?? ''
        );
    }
}