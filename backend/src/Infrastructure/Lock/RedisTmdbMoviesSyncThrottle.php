<?php

namespace App\Infrastructure\Lock;

use App\Application\Lock\MoviesSyncThrottleInterface;
use DateTimeImmutable;
use Predis\Client;

final class RedisTmdbMoviesSyncThrottle implements MoviesSyncThrottleInterface
{
    public function __construct(
        private readonly Client $redis,
    ) {}

    public function nextAvailableAt(string $lockName): ?DateTimeImmutable
    {
        $ttl = $this->redis->ttl($lockName);

        if ($ttl <= 0) {
            return null;
        }

        $nextAvailable = (new \DateTimeImmutable())->modify("+{$ttl} seconds");

        return $nextAvailable !== false ? $nextAvailable : null;
    }

}
