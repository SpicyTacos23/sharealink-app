<?php

namespace App\Infrastructure\DataTransformer\TMDB\Helper;

final class TmdbStringHelper
{
    public static function str(mixed $value): string
    {
        return (string) ($value ?? '');
    }
}
