<?php 

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowEpisodes;

final class LoadShowEpisodesRequest
{
    public function __construct(
        public readonly string $id,
        public readonly int $seasonNumber
    ) {}
}