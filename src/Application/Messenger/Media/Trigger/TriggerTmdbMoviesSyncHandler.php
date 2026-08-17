<?php

namespace App\Application\Messenger\Media\Trigger;

use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;
use App\Application\Messenger\Media\Message\SyncMoviesFromCacheMessage;
use App\Application\Lock\MoviesSyncThrottleInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\DTO\TMDB\SyncTriggerResult;
use Symfony\Component\Lock\LockFactory;
use Zenstruck\Messenger\Monitor\Stamp\TagStamp;

final class TriggerTmdbMoviesSyncHandler
{
    private const LOCK_NAME = 'tmdb_sync_movies';
    private const TTL_SECONDS = 8 * 3600;

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly MessageBusInterface $bus,
        private readonly TmdbCacheKeyGeneratorInterface $keyGenerator,
        private readonly MoviesSyncThrottleInterface $throttle
    ) {}

    /**
     * @param array<mixed> $filters
     */
    private function lockNameForFilters(array $filters): string
    {
        if (empty($filters)) {
            return self::LOCK_NAME . '_default';
        }

        $encoded = json_encode($filters);
        if ($encoded === false) {
            throw new \RuntimeException('Unable to encode filters for lock name generation');
        }

        return self::LOCK_NAME . '_' . md5($encoded);
    }

    /**
     * @param array<mixed> $filters
     */
    public function handle(array $filters = []): SyncTriggerResult
    {
        $lockName = $this->lockNameForFilters($filters);

        $lock = $this->lockFactory->createLock(
            resource: $lockName,
            ttl: self::TTL_SECONDS,
            autoRelease: false,
        );

        if (!$lock->acquire()) {
            $nextAvailableAt = $this->throttle->nextAvailableAt($lockName);

            return SyncTriggerResult::alreadyRunning(
                retryAfterSeconds: $nextAvailableAt
                    ? $nextAvailableAt->getTimestamp() - (new \DateTimeImmutable())->getTimestamp()
                    : null,
                nextAvailableAt: $nextAvailableAt,
            );
        }

        //Necesitamos el Key que ha generado el servicio para recuperar los datos de cache
        $cacheKey = "tmdb_" . $this->keyGenerator->forMovies($filters);

        // Lanza el mensaje para procesar la caché
        $this->bus->dispatch(new SyncMoviesFromCacheMessage($cacheKey), [
            new TagStamp('data')
        ]);

        return SyncTriggerResult::dispatched();
    }
}
