<?php

namespace App\Tests\Infrastructure\Lock;

use App\Infrastructure\Lock\RedisTmdbMoviesSyncThrottle;
use App\Infrastructure\Lock\RedisTmdbShowsSyncThrottle;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Predis\Client;

final class RedisTmdbSyncThrottleTest extends TestCase
{
    public function testReturnsNullWhenLockHasNoRemainingTtl(): void
    {
        $redis = new InMemoryPredisClient(0);

        self::assertNull((new RedisTmdbMoviesSyncThrottle($redis))->nextAvailableAt('movies'));
        self::assertNull((new RedisTmdbShowsSyncThrottle($redis))->nextAvailableAt('shows'));
    }

    public function testReturnsNextAvailableDateForPositiveTtl(): void
    {
        $redis = new InMemoryPredisClient(60);
        $minimumTimestamp = time() + 60;

        $moviesDate = (new RedisTmdbMoviesSyncThrottle($redis))->nextAvailableAt('movies');
        $showsDate = (new RedisTmdbShowsSyncThrottle($redis))->nextAvailableAt('shows');

        self::assertInstanceOf(DateTimeImmutable::class, $moviesDate);
        self::assertInstanceOf(DateTimeImmutable::class, $showsDate);
        self::assertGreaterThanOrEqual($minimumTimestamp, $moviesDate->getTimestamp());
        self::assertGreaterThanOrEqual($minimumTimestamp, $showsDate->getTimestamp());
    }
}

final class InMemoryPredisClient extends Client
{
    public function __construct(private readonly int $ttl) {}

    public function ttl(string $key): int
    {
        return $this->ttl;
    }
}
