<?php

namespace App\Tests\Application\Messenger\Media\Handler;

use App\Application\Messenger\Media\Handler\SyncTmdbMoviesFromCacheHandler;
use App\Application\Messenger\Media\Message\SyncMoviesFromCacheMessage;
use App\Domain\Entity\Movie;
use App\Domain\Repository\MovieRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class SyncTmdbMoviesFromCacheHandlerTest extends TestCase
{
    private CacheInterface&MockObject $cache;
    private MovieRepositoryInterface&MockObject $movieRepository;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->movieRepository = $this->createMock(MovieRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function makeHandler(): SyncTmdbMoviesFromCacheHandler
    {
        return new SyncTmdbMoviesFromCacheHandler(
            $this->cache,
            $this->movieRepository,
            $this->logger,
        );
    }

    public function testReturnsEmptyStringWhenCacheMisses(): void
    {
        $message = new SyncMoviesFromCacheMessage('tmdb_movies_cache_key');

        $this->cache->expects(self::once())
            ->method('get')
            ->with(
                $message->cacheKey,
                self::callback(static fn($callback): bool => is_callable($callback)),
            )
            ->willReturn(null);

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('TMDB sync: cache miss, nothing to process', [
                'key' => $message->cacheKey,
            ]);

        $this->movieRepository->expects(self::never())
            ->method('findExistingTmdbIds');

        $this->movieRepository->expects(self::never())
            ->method('bulkInsert');

        self::assertSame('', $this->makeHandler()($message));
    }

    public function testInsertsOnlyNewMoviesAndReturnsSummaryMessage(): void
    {
        $message = new SyncMoviesFromCacheMessage('tmdb_movies_cache_key');

        $cachePayload = [
            'results' => [
                [
                    'id' => 101,
                    'title' => 'Film A',
                    'overview' => 'Description A',
                    'poster_path' => '/poster-a.jpg',
                    'backdrop_path' => '/backdrop-a.jpg',
                    'release_date' => '2024-01-01',
                ],
                [
                    'id' => 202,
                    'title' => 'Film B',
                    'overview' => 'Description B',
                    'poster_path' => '/poster-b.jpg',
                    'backdrop_path' => '/backdrop-b.jpg',
                    'release_date' => '2024-02-02',
                ],
            ],
        ];

        $this->cache->expects(self::once())
            ->method('get')
            ->with(
                $message->cacheKey,
                self::callback(static fn($callback): bool => is_callable($callback)),
            )
            ->willReturn($cachePayload);

        $this->movieRepository->expects(self::once())
            ->method('findExistingTmdbIds')
            ->with([101, 202])
            ->willReturn([101]);

        $this->movieRepository->expects(self::once())
            ->method('bulkInsert')
            ->with(self::callback(static fn(array $movies): bool =>
                count($movies) === 1
                && $movies[0] instanceof Movie
                && $movies[0]->getTitle() === 'Film B'
                && $movies[0]->getMovieId() === '202'
            ));

        $this->logger->expects(self::once())
            ->method('info')
            ->with('TMDB sync completada', [
                'total' => 2,
                'nuevos' => 1,
            ]);

        self::assertSame(
            'TMDB sync movies completed. Total: 2, nuevos: 1',
            $this->makeHandler()($message),
        );
    }

    public function testDoesNotInsertWhenResultsAreEmpty(): void
    {
        $message = new SyncMoviesFromCacheMessage('tmdb_movies_cache_key');

        $this->cache->expects(self::once())
            ->method('get')
            ->with(
                $message->cacheKey,
                self::callback(static fn($callback): bool => is_callable($callback)),
            )
            ->willReturn(['results' => []]);

        $this->movieRepository->expects(self::never())
            ->method('findExistingTmdbIds');

        $this->movieRepository->expects(self::never())
            ->method('bulkInsert');

        $this->logger->expects(self::never())
            ->method('info');

        self::assertSame('', $this->makeHandler()($message));
    }
}
