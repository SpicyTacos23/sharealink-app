<?php

namespace App\Application\Lock;

use DateTimeImmutable;

interface ShowsSyncThrottleInterface
{
    public function nextAvailableAt(string $lockName): ?DateTimeImmutable;
}