<?php

namespace App\Application\Messenger\Media\Trigger;

use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;
use App\Application\Messenger\Media\Message\SyncShowsFromCacheMessage;
use App\Application\Lock\ShowsSyncThrottleInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\DTO\TMDB\SyncTriggerResult;
use Zenstruck\Messenger\Monitor\Stamp\TagStamp;

final class TriggerTmdbShowsSyncHandler
{
    private const LOCK_NAME = 'tmdb_sync_shows';
    private const TTL_SECONDS = 8 * 3600;

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly MessageBusInterface $bus,
        private readonly TmdbCacheKeyGeneratorInterface $keyGenerator,
        private readonly ShowsSyncThrottleInterface $throttle,
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
        $cacheKey = "tmdb_" . $this->keyGenerator->forShows($filters);

        $this->bus->dispatch(new SyncShowsFromCacheMessage($cacheKey), [
            new TagStamp('data')
        ]);

        return SyncTriggerResult::dispatched();
    }
}
