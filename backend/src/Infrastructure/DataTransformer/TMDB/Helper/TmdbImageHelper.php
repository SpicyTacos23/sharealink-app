<?php

namespace App\Infrastructure\DataTransformer\TMDB\Helper;

final class TmdbImageHelper
{
    private const IMAGE_BASE = 'https://image.tmdb.org/t/p/';

    public static function url(?string $path, string $size): string
    {
        return $path ? self::IMAGE_BASE . $size . '/' . ltrim($path, '/') : '';
    }
}
