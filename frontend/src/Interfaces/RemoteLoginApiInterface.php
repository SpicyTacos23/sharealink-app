<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Cookie;

interface RemoteLoginApiInterface
{
    public function getToken(array $data): Cookie;
}