<?php

namespace App\Tests\Application\Messenger\Media\Trigger;

use App\Application\Encoder\TmdbCacheKeyGeneratorInterface;
use App\Application\Lock\ShowsSyncThrottleInterface;
use App\Application\Messenger\Media\Message\SyncShowsFromCacheMessage;
use App\Application\Messenger\Media\Trigger\TriggerTmdbShowsSyncHandler;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;

/**
 * IMPORTANTE: usamos InMemoryStore en lugar de Redis real.
 * InMemoryStore respeta TTL con el reloj real del sistema, así que replica
 * el comportamiento de mutex + cooldown sin necesitar infraestructura,
 * siempre que el test se ejecute en menos tiempo que el TTL configurado.
 */
final class TriggerShowsSyncHandlerTest extends TestCase
{
    private LockFactory $lockFactory;
    private MessageBusInterface&\PHPUnit\Framework\MockObject\MockObject $bus;
    private TmdbApiCallerInterface&\PHPUnit\Framework\MockObject\MockObject $apiCaller;
    private ShowsSyncThrottleInterface&\PHPUnit\Framework\MockObject\MockObject $throttle;
    private TmdbCacheKeyGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject $keyGenerator;

    protected function setUp(): void
    {
        $this->lockFactory = new LockFactory(new InMemoryStore());
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->apiCaller = $this->createMock(TmdbApiCallerInterface::class);
        $this->throttle = $this->createMock(ShowsSyncThrottleInterface::class);
        $this->keyGenerator = $this->createMock(TmdbCacheKeyGeneratorInterface::class);
    }

    private function makeHandler(): TriggerTmdbShowsSyncHandler
    {
        return new TriggerTmdbShowsSyncHandler(
            $this->lockFactory,
            $this->bus,
            $this->apiCaller,
            $this->keyGenerator,
            $this->throttle,
        );
    }

    public function testFirstCallAcquiresLockAndDispatchesMessage(): void
    {
        $this->apiCaller->expects(self::once())
            ->method('getShowsWithFilters')
            ->with([]);

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(SyncShowsFromCacheMessage::class))
            ->willReturn(new Envelope(new SyncShowsFromCacheMessage('shows_' . md5('[]'))));

        $handler = $this->makeHandler();
        $result = $handler->handle([]);

        self::assertTrue($result->dispatched);
        self::assertFalse($result->isAlreadyRunning());
        self::assertNull($result->retryAfterSeconds);
    }

    public function testSecondCallWithinCooldownIsBlockedAndDoesNotDispatchAgain(): void
    {
        // El mensaje solo debe dispatcharse UNA vez en total, no dos.
        $this->bus->expects(self::once())->method('dispatch')
            ->willReturn(new Envelope(new SyncShowsFromCacheMessage('irrelevant')));

        $this->apiCaller->expects(self::once())->method('getShows');

        $nextAvailableAt = (new \DateTimeImmutable())->modify('+8 hours');
        $this->throttle->expects(self::once())
            ->method('nextAvailableAt')
            ->willReturn($nextAvailableAt);

        $handler = $this->makeHandler();

        $first = $handler->handle([]);
        self::assertTrue($first->dispatched);

        // Segunda llamada inmediata: el mismo LockFactory (misma instancia,
        // mismo InMemoryStore) todavía tiene el lock activo.
        $second = $handler->handle([]);

        self::assertFalse($second->dispatched);
        self::assertTrue($second->isAlreadyRunning());
        self::assertNotNull($second->retryAfterSeconds);
        self::assertGreaterThan(0, $second->retryAfterSeconds);
    }

    public function testAlreadyRunningWithoutThrottleDataReturnsNullRetryAfter(): void
    {
        $this->bus->expects(self::once())->method('dispatch')
            ->willReturn(new Envelope(new SyncShowsFromCacheMessage('irrelevant')));

        $this->apiCaller->expects(self::once())->method('getShows');

        // Simulamos que el throttle no tiene datos (p.ej. Redis desincronizado)
        $this->throttle->method('nextAvailableAt')->willReturn(null);

        $handler = $this->makeHandler();
        $handler->handle([]);
        $result = $handler->handle([]);

        self::assertTrue($result->isAlreadyRunning());
        self::assertNull($result->retryAfterSeconds);
        self::assertNull($result->nextAvailableAt);
    }
}