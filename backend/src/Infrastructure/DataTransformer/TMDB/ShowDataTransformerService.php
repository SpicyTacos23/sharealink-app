<?php

namespace App\Infrastructure\DataTransformer\TMDB;

use App\Application\DataTransformer\TMDB\ShowDataTransformerInterface;
use App\Application\DTO\TMDB\CastMemberDto;
use App\Application\DTO\TMDB\CrewMemberDto;
use App\Application\DTO\TMDB\ShowCreditsDto;
use App\Application\DTO\TMDB\ShowDetailsDto;
use App\Application\DTO\TMDB\ShowDto;
use App\Application\DTO\TMDB\ShowEpisodeDto;
use App\Application\DTO\TMDB\ShowImagesDto;
use App\Application\DTO\TMDB\ShowSeasonDto;
use App\Application\DTO\TMDB\ImageDto;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbArrayHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbDateHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbImageHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbStringHelper;

final class ShowDataTransformerService implements ShowDataTransformerInterface
{
    public function transformShow(array $data): ShowDto
    {
        return new ShowDto(
            adult: (bool) ($data['adult'] ?? false),
            backdropPath: TmdbImageHelper::url($data['backdrop_path'] ?? null, 'w1280'),
            genreIds: $data['genre_ids'] ?? [],
            id: (int) ($data['id'] ?? 0),
            originCountry: $data['origin_country'] ?? [],
            originalLanguage: TmdbStringHelper::str($data['original_language'] ?? ''),
            originalName: TmdbStringHelper::str($data['original_name'] ?? ''),
            overview: TmdbStringHelper::str($data['overview'] ?? ''),
            popularity: (float) ($data['popularity'] ?? 0),
            posterPath: TmdbImageHelper::url($data['poster_path'] ?? null, 'w1280'),
            firstAirDate: $data['first_air_date'] ?? null,
            softcore: (bool) ($data['softcore'] ?? false),
            name: TmdbStringHelper::str($data['name'] ?? ''),
            voteAverage: (float) ($data['vote_average'] ?? 0),
            voteCount: (int) ($data['vote_count'] ?? 0),
            type: 'tv'
        );
    }

    public function transformShowDetails(array $data): ShowDetailsDto
    {
        return new ShowDetailsDto(
            id: (int) ($data['id'] ?? 0),
            adult: (bool) ($data['adult'] ?? false),
            backdropPath: TmdbImageHelper::url($data['backdrop_path'] ?? null, 'w1280'),
            createdBy: TmdbArrayHelper::values($data['created_by'] ?? []),
            episodeRunTime: TmdbArrayHelper::values($data['episode_run_time'] ?? []),
            firstAirDate: TmdbDateHelper::nullable($data['first_air_date'] ?? null),
            genres: TmdbArrayHelper::mapNames($data['genres'] ?? []),
            homepage: TmdbStringHelper::str($data['homepage'] ?? ''),
            inProduction: (bool) ($data['in_production'] ?? false),
            languages: TmdbArrayHelper::values($data['languages'] ?? []),
            lastAirDate: TmdbDateHelper::nullable($data['last_air_date'] ?? null),
            lastEpisodeToAir: $data['last_episode_to_air'] ?? null,
            name: TmdbStringHelper::str($data['name'] ?? ''),
            nextEpisodeToAir: $data['next_episode_to_air'] ?? [],
            networks: TmdbArrayHelper::values($data['networks'] ?? []),
            numberOfEpisodes: (int) ($data['number_of_episodes'] ?? 0),
            numberOfSeasons: (int) ($data['number_of_seasons'] ?? 0),
            originCountry: TmdbArrayHelper::values($data['origin_country'] ?? []),
            originalLanguage: TmdbStringHelper::str($data['original_language'] ?? ''),
            originalName: TmdbStringHelper::str($data['original_name'] ?? ''),
            overview: TmdbStringHelper::str($data['overview'] ?? ''),
            popularity: (float) ($data['popularity'] ?? 0),
            posterPath: TmdbImageHelper::url($data['poster_path'] ?? null, 'w780'),
            productionCompanies: TmdbArrayHelper::values($data['production_companies'] ?? []),
            productionCountries: TmdbArrayHelper::values($data['production_countries'] ?? []),
            seasons: TmdbArrayHelper::values($data['seasons'] ?? []),
            softcore: (bool) ($data['softcore'] ?? false),
            spokenLanguages: TmdbArrayHelper::values($data['spoken_languages'] ?? []),
            status: TmdbStringHelper::str($data['status'] ?? ''),
            tagline: TmdbStringHelper::str($data['tagline'] ?? ''),
            type: TmdbStringHelper::str($data['type'] ?? ''),
            voteAverage: (float) ($data['vote_average'] ?? 0),
            voteCount: (int) ($data['vote_count'] ?? 0),
        );
    }

    public function transformShowCredits(array $data): ShowCreditsDto
    {
        $cast = array_map(
            static fn(array $m): CastMemberDto => new CastMemberDto(
                id: (int) ($m['id'] ?? 0),
                adult: (bool) ($m['adult'] ?? false),
                gender: (int) ($m['gender'] ?? 0),
                knownForDepartment: TmdbStringHelper::str($m['known_for_department'] ?? ''),
                name: TmdbStringHelper::str($m['name'] ?? ''),
                originalName: TmdbStringHelper::str($m['original_name'] ?? ''),
                popularity: (float) ($m['popularity'] ?? 0.0),
                profilePath: $m['profile_path'] ?? null,
                castId: (int) ($m['cast_id'] ?? 0),
                character: TmdbStringHelper::str($m['character'] ?? ''),
                creditId: TmdbStringHelper::str($m['credit_id'] ?? ''),
                order: (int) ($m['order'] ?? 0),
            ),
            $data['cast'] ?? []
        );

        $crew = array_map(
            static fn(array $m): CrewMemberDto => new CrewMemberDto(
                id: (int) ($m['id'] ?? 0),
                adult: (bool) ($m['adult'] ?? false),
                gender: (int) ($m['gender'] ?? 0),
                knownForDepartment: TmdbStringHelper::str($m['known_for_department'] ?? ''),
                name: TmdbStringHelper::str($m['name'] ?? ''),
                originalName: TmdbStringHelper::str($m['original_name'] ?? ''),
                popularity: (float) ($m['popularity'] ?? 0.0),
                profilePath: $m['profile_path'] ?? null,
                creditId: TmdbStringHelper::str($m['credit_id'] ?? ''),
                department: TmdbStringHelper::str($m['department'] ?? ''),
                job: TmdbStringHelper::str($m['job'] ?? ''),
            ),
            $data['crew'] ?? []
        );

        return new ShowCreditsDto(
            id: (int) ($data['id'] ?? 0),
            cast: $cast,
            crew: $crew,
        );
    }

    public function transformShowSeasons(array $data): ShowSeasonDto
    {
        $episodes = array_map(
            fn(array $ep): ShowEpisodeDto => new ShowEpisodeDto(
                id: (int) ($ep['id'] ?? 0),
                airDate: TmdbDateHelper::nullable($ep['air_date'] ?? null),
                episodeNumber: (int) ($ep['episode_number'] ?? 0),
                episodeType: TmdbStringHelper::str($ep['episode_type'] ?? ''),
                name: TmdbStringHelper::str($ep['name'] ?? ''),
                overview: TmdbStringHelper::str($ep['overview'] ?? ''),
                productionCode: TmdbStringHelper::str($ep['production_code'] ?? ''),
                runtime: isset($ep['runtime']) ? (int) $ep['runtime'] : null,
                seasonNumber: (int) ($ep['season_number'] ?? 0),
                showId: (int) ($ep['show_id'] ?? 0),
                stillPath: TmdbImageHelper::url($ep['still_path'] ?? null, 'w780'),
                voteAverage: (float) ($ep['vote_average'] ?? 0),
                voteCount: (int) ($ep['vote_count'] ?? 0),
                crew: TmdbArrayHelper::values($ep['crew'] ?? []),
                guestStars: TmdbArrayHelper::values($ep['guest_stars'] ?? []),
            ),
            $data['episodes'] ?? []
        );

        return new ShowSeasonDto(
            _id: TmdbStringHelper::str($data['_id'] ?? ''),
            airDate: TmdbDateHelper::nullable($data['air_date'] ?? null),
            episodes: $episodes,
            name: TmdbStringHelper::str($data['name'] ?? ''),
            networks: TmdbArrayHelper::values($data['networks'] ?? []),
            overview: TmdbStringHelper::str($data['overview'] ?? ''),
            id: (int) ($data['id'] ?? 0),
            posterPath: TmdbImageHelper::url($data['poster_path'] ?? null, 'w780'),
            seasonNumber: (int) ($data['season_number'] ?? 0),
            voteAverage: (float) ($data['vote_average'] ?? 0),
        );
    }

    public function transformShowImages(array $data): ShowImagesDto
    {
        $transformImage = fn(array $img): ImageDto => new ImageDto(
            aspectRatio: (float) ($img['aspect_ratio'] ?? 0),
            height: (int) ($img['height'] ?? 0),
            iso: TmdbStringHelper::str($img['iso_639_1'] ?? $img['iso_3166_1'] ?? ''),
            filePath: TmdbImageHelper::url($img['file_path'] ?? null, 'w780'),
            voteAverage: (float) ($img['vote_average'] ?? 0),
            voteCount: (int) ($img['vote_count'] ?? 0),
            width: (int) ($img['width'] ?? 0),
        );

        return new ShowImagesDto(
            _id: TmdbStringHelper::str($data['id'] ?? ''),
            backdrops: array_map($transformImage, $data['backdrops'] ?? []),
            logos: array_map($transformImage, $data['logos'] ?? []),
            posters: array_map($transformImage, $data['posters'] ?? []),
        );
    }
}
