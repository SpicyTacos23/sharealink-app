<?php

namespace App\Tests\Application\Messenger\Media\Trigger;

use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;
use App\Application\Lock\ShowsSyncThrottleInterface;
use App\Application\Messenger\Media\Message\SyncShowsFromCacheMessage;
use App\Application\Messenger\Media\Trigger\TriggerTmdbShowsSyncHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Uses InMemoryStore instead of a real Redis instance.
 */
final class TriggerShowsSyncHandlerTest extends TestCase
{
    private LockFactory $lockFactory;
    private MessageBusInterface&MockObject $bus;
    private ShowsSyncThrottleInterface&MockObject $throttle;
    private TmdbCacheKeyGeneratorInterface&MockObject $keyGenerator;

    protected function setUp(): void
    {
        $this->lockFactory = new LockFactory(new InMemoryStore());
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->throttle = $this->createMock(ShowsSyncThrottleInterface::class);
        $this->keyGenerator = $this->createMock(TmdbCacheKeyGeneratorInterface::class);
    }

    private function makeHandler(): TriggerTmdbShowsSyncHandler
    {
        return new TriggerTmdbShowsSyncHandler(
            $this->lockFactory,
            $this->bus,
            $this->keyGenerator,
            $this->throttle,
        );
    }

    public function testFirstCallAcquiresLockAndDispatchesMessage(): void
    {
        $this->keyGenerator->expects(self::once())
            ->method('forShows')
            ->with([])
            ->willReturn('shows_cache_key');

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->with(
                self::callback(static fn(object $message): bool =>
                    $message instanceof SyncShowsFromCacheMessage
                    && $message->cacheKey === 'tmdb_shows_cache_key'
                ),
                self::isType('array'),
            )
            ->willReturnCallback(static fn(object $message, array $stamps = []): Envelope =>
                new Envelope($message, $stamps)
            );

        $result = $this->makeHandler()->handle([]);

        self::assertTrue($result->dispatched);
        self::assertFalse($result->isAlreadyRunning());
        self::assertNull($result->retryAfterSeconds);
    }

    public function testSecondCallWithinCooldownIsBlockedAndDoesNotDispatchAgain(): void
    {
        $this->keyGenerator->expects(self::once())
            ->method('forShows')
            ->with([])
            ->willReturn('shows_cache_key');

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(static fn(object $message, array $stamps = []): Envelope =>
                new Envelope($message, $stamps)
            );

        $nextAvailableAt = (new \DateTimeImmutable())->modify('+8 hours');
        $this->throttle->expects(self::once())
            ->method('nextAvailableAt')
            ->willReturn($nextAvailableAt);

        $handler = $this->makeHandler();

        $first = $handler->handle([]);
        self::assertTrue($first->dispatched);

        $second = $handler->handle([]);

        self::assertFalse($second->dispatched);
        self::assertTrue($second->isAlreadyRunning());
        self::assertNotNull($second->retryAfterSeconds);
        self::assertGreaterThan(0, $second->retryAfterSeconds);
    }

    public function testAlreadyRunningWithoutThrottleDataReturnsNullRetryAfter(): void
    {
        $this->keyGenerator->expects(self::once())
            ->method('forShows')
            ->with([])
            ->willReturn('shows_cache_key');

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(static fn(object $message, array $stamps = []): Envelope =>
                new Envelope($message, $stamps)
            );

        $this->throttle->expects(self::once())
            ->method('nextAvailableAt')
            ->willReturn(null);

        $handler = $this->makeHandler();
        $handler->handle([]);
        $result = $handler->handle([]);

        self::assertTrue($result->isAlreadyRunning());
        self::assertNull($result->retryAfterSeconds);
        self::assertNull($result->nextAvailableAt);
    }
}
