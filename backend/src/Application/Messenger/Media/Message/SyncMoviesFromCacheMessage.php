<?php

namespace App\Application\Messenger\Media\Message;

final class SyncMoviesFromCacheMessage
{
    public function __construct(public readonly string $cacheKey){}
}