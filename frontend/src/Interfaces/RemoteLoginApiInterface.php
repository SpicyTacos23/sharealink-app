<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Cookie;

interface RemoteLoginApiInterface
{
    /**
     * @param array<mixed> $data
     */
    public function getToken(array $data): Cookie;
}