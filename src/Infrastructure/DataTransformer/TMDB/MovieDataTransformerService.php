<?php

namespace App\Infrastructure\DataTransformer\TMDB;

use App\Application\DataTransformer\TMDB\MovieDataTransformerInterface;
use App\Application\DTO\TMDB\CastMemberDto;
use App\Application\DTO\TMDB\CrewMemberDto;
use App\Application\DTO\TMDB\ImageDto;
use App\Application\DTO\TMDB\MovieCreditsDto;
use App\Application\DTO\TMDB\MovieDetailsDto;
use App\Application\DTO\TMDB\MovieDto;
use App\Application\DTO\TMDB\MovieImagesDto;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbArrayHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbDateHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbImageHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbStringHelper;

final class MovieDataTransformerService implements MovieDataTransformerInterface
{
    public function transformMovie(array $data): MovieDto
    {
        return new MovieDto(
            adult: (bool) ($data['adult'] ?? false),
            backdropPath: TmdbImageHelper::url($data['backdrop_path'] ?? null, 'w1280'),
            genres: TmdbArrayHelper::values($data['genre_ids'] ?? []),
            id: TmdbStringHelper::str($data['id'] ?? ''),
            title: TmdbStringHelper::str($data['title'] ?? ''),
            originalTitle: TmdbStringHelper::str($data['original_title'] ?? $data['title'] ?? ''),
            originalLanguage: TmdbStringHelper::str($data['original_language'] ?? ''),
            overview: TmdbStringHelper::str($data['overview'] ?? ''),
            popularity: (int) round((float) ($data['popularity'] ?? 0)),
            posterPath: TmdbImageHelper::url($data['poster_path'] ?? null, 'w780'),
            releaseDate: TmdbDateHelper::default($data['release_date'] ?? null),
            softcore: false,
            video: (bool) ($data['video'] ?? false),
            voteAverage: (int) round((float) ($data['vote_average'] ?? 0)),
            voteCount: (int) ($data['vote_count'] ?? 0)
        );
    }

    public function transformMovieDetails(array $data): MovieDetailsDto
    {
        return new MovieDetailsDto(
            id: TmdbStringHelper::str($data['id'] ?? ''),
            imdbId: TmdbStringHelper::str($data['imdb_id'] ?? ''),
            title: TmdbStringHelper::str($data['title'] ?? ''),
            originalTitle: TmdbStringHelper::str($data['original_title'] ?? $data['title'] ?? ''),
            originalLanguage: TmdbStringHelper::str($data['original_language'] ?? ''),
            overview: TmdbStringHelper::str($data['overview'] ?? ''),
            homepage: TmdbStringHelper::str($data['homepage'] ?? ''),
            status: TmdbStringHelper::str($data['status'] ?? ''),
            tagline: TmdbStringHelper::str($data['tagline'] ?? ''),
            adult: (bool) ($data['adult'] ?? false),
            backdropPath: TmdbImageHelper::url($data['backdrop_path'] ?? null, 'w1280'),
            genres: TmdbArrayHelper::mapNames($data['genres'] ?? []),
            posterPath: TmdbImageHelper::url($data['poster_path'] ?? null, 'w780'),
            releaseDate: TmdbDateHelper::nullable($data['release_date'] ?? null),
            runtime: (int) ($data['runtime'] ?? 0),
            video: (bool) ($data['video'] ?? false),
            voteAverage: (float) ($data['vote_average'] ?? 0),
            voteCount: (int) ($data['vote_count'] ?? 0),
            budget: (int) ($data['budget'] ?? 0),
            revenue: (int) ($data['revenue'] ?? 0),
            productionCompanies: TmdbArrayHelper::values($data['production_companies'] ?? []),
            productionCountries: TmdbArrayHelper::values($data['production_countries'] ?? []),
            originCountry: TmdbArrayHelper::values($data['origin_country'] ?? []),
            spokenLanguages: TmdbArrayHelper::values($data['spoken_languages'] ?? []),
            belongsToCollection: $data['belongs_to_collection'] ?? []
        );
    }

    public function transformMovieCredits(array $data): MovieCreditsDto
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

        return new MovieCreditsDto(
            id: (int) ($data['id'] ?? 0),
            cast: $cast,
            crew: $crew,
        );
    }

    public function transformMovieImages(array $data): MovieImagesDto
    {
        $transformImage = fn(array $img): ImageDto => new ImageDto(
            aspectRatio: (float) ($img['aspect_ratio'] ?? 0),
            height: (int) ($img['height'] ?? 0),
            iso: TmdbStringHelper::str($img['iso_639_1'] ?? $img['iso_3166_1'] ?? ''),
            filePath: TmdbImageHelper::url($img['file_path'] ?? null, 'w500'),
            voteAverage: (float) ($img['vote_average'] ?? 0),
            voteCount: (int) ($img['vote_count'] ?? 0),
            width: (int) ($img['width'] ?? 0),
        );

        return new MovieImagesDto(
            _id: TmdbStringHelper::str($data['id'] ?? ''),
            backdrops: array_map($transformImage, $data['backdrops'] ?? []),
            logos: array_map($transformImage, $data['logos'] ?? []),
            posters: array_map($transformImage, $data['posters'] ?? []),
        );
    }
}
