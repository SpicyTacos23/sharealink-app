<?php

namespace App\Infrastructure\DataTransformer\TMDB;

use App\Application\DataTransformer\TMDB\PersonDataTransformerInterface;
use App\Application\DTO\TMDB\PersonCreditsDto;
use App\Application\DTO\TMDB\PersonDetailsDto;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbDateHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbImageHelper;
use App\Infrastructure\DataTransformer\TMDB\Helper\TmdbStringHelper;

final class PersonDataTransformerService implements PersonDataTransformerInterface
{
    public function transformPersonDetails(array $data): PersonDetailsDto
    {
        return new PersonDetailsDto(
            id: (int) ($data['id'] ?? 0),
            adult: (bool) ($data['adult'] ?? false),
            alsoKnownAs: array_values($data['also_known_as'] ?? []),
            biography: $data['biography'] ?? null,
            birthday: TmdbDateHelper::nullable($data['birthday'] ?? null),
            deathday: TmdbDateHelper::nullable($data['deathday'] ?? null),
            gender: (int) ($data['gender'] ?? 0),
            homepage: $data['homepage'] ?? null,
            imdbId: TmdbStringHelper::str($data['imdb_id'] ?? ''),
            knownForDepartment: TmdbStringHelper::str($data['known_for_department'] ?? ''),
            name: TmdbStringHelper::str($data['name'] ?? ''),
            placeOfBirth: $data['place_of_birth'] ?? null,
            popularity: (float) ($data['popularity'] ?? 0.0),
            profilePath: TmdbImageHelper::url($data['profile_path'] ?? null, 'w780'),
        );
    }

    public function transformPersonCredits(array $data): PersonCreditsDto
    {
        return new PersonCreditsDto(
            character: TmdbStringHelper::str($data['character'] ?? ''),
            creditId: TmdbStringHelper::str($data['credit_id'] ?? ''),
            order: (int) ($data['order'] ?? 0),
            mediaType: TmdbStringHelper::str($data['media_type'] ?? 'movie')
        );
    }
}
