<?php

namespace App\Application\Messenger\Media\Message;

final class SyncShowsFromCacheMessage
{
    public function __construct(public readonly string $cacheKey){}
}