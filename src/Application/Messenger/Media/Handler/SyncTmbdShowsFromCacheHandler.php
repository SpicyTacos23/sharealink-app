<?php

namespace App\Application\Messenger\Media\Handler;

use App\Application\Messenger\Media\Message\SyncShowsFromCacheMessage;
use App\Domain\Entity\Show;
use App\Domain\Repository\ShowRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncTmbdShowsFromCacheHandler
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly ShowRepositoryInterface $showRepository
    ) {}

    public function __invoke(SyncShowsFromCacheMessage $message): string
    {
        $item = $this->cache->getItem($message->cacheKey);

        if (!$item->isHit()) {
            $this->logger->warning('TMDB sync: cache miss, nothing to process', [
                'key' => $message->cacheKey,
            ]);
            return '';
        }

        $data = $item->get();

        $results = $data['results'] ?? [];
        if (empty($results)) {
            $this->logger->warning('TMDB sync: no data found with this parameters', [
                'key' => $message->cacheKey,
            ]);
            return '';
        }

        $incomingIds = array_map('intval', array_column($results, 'id'));
        $existingIds = array_map('intval', $this->showRepository->findExistingTmdbIds($incomingIds));

        $toInsert = [];

        foreach ($results as $raw) {
            $tmdbId = isset($raw['id']) ? (int) $raw['id'] : null;

            if (!$tmdbId || in_array($tmdbId, $existingIds, true)) {
                continue;
            }

            $toInsert[] = Show::createFromTmdbPayload($raw);
        }

        if (!empty($toInsert)) {
            $this->showRepository->bulkInsert($toInsert);
        }

        $this->logger->info('TMDB sync completada', [
            'total' => count($results),
            'nuevos' => count($toInsert),
        ]);

        return "TMDB sync shows completed. Total: " . count($results) . ", nuevos: " . count($toInsert);
    }
}
