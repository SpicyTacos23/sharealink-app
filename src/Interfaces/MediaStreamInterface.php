<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Response;

interface MediaStreamInterface
{
    public function handleMediaStream(int $id): Response;
}