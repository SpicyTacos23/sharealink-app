<?php

namespace App\Infrastructure\DataTransformer\TMDB\Helper;

final class TmdbArrayHelper
{
    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public static function values(?array $data): array
    {
        return array_values($data ?? []);
    }

    /**
     * @param array<mixed> $genres
     * @return array<mixed>
     */
    public static function mapNames(?array $genres): array
    {
        return array_map(
            static fn(array $g): string => (string) ($g['name'] ?? ''),
            $genres ?? []
        );
    }
}
