<?php

namespace App\Application\Lock;

use DateTimeImmutable;

interface MoviesSyncThrottleInterface
{
    public function nextAvailableAt(string $lockName): ?DateTimeImmutable;
}
