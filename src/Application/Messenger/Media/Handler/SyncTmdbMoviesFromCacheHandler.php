<?php

namespace App\Application\Messenger\Media\Handler;

use App\Application\Messenger\Media\Message\SyncMoviesFromCacheMessage;
use App\Domain\Entity\Movie;
use App\Domain\Repository\MovieRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncTmdbMoviesFromCacheHandler
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly MovieRepositoryInterface $movieRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(SyncMoviesFromCacheMessage $message): string
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
            return '';
        }

        $incomingIds = array_map('intval', array_column($results, 'id'));
        $existingIds = array_map('intval', $this->movieRepository->findExistingTmdbIds($incomingIds));

        $toInsert = [];

        foreach ($results as $raw) {
            $tmdbId = isset($raw['id']) ? (int) $raw['id'] : null;

            if (!$tmdbId || in_array($tmdbId, $existingIds, true)) {
                continue;
            }

            $toInsert[] = Movie::createFromTmdbPayload($raw);
        }

        if (!empty($toInsert)) {
            $this->movieRepository->bulkInsert($toInsert);
        }

        $this->logger->info('TMDB sync completada', [
            'total' => count($results),
            'nuevos' => count($toInsert),
        ]);

        return "TMDB sync movies completed. Total: " . count($results) . ", nuevos: " . count($toInsert);
    }
}