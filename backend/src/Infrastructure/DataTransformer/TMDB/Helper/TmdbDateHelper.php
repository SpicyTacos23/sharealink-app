<?php

namespace App\Infrastructure\DataTransformer\TMDB\Helper;

use DateTime;

final class TmdbDateHelper
{
    private const DEFAULT_DATE = '1970-01-01';

    public static function nullable(?string $date): ?DateTime
    {
        return $date ? new DateTime($date) : null;
    }

    public static function default(?string $date): DateTime
    {
        return $date ? new DateTime($date) : new DateTime(self::DEFAULT_DATE);
    }
}
