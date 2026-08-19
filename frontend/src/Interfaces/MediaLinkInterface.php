<?php

namespace App\Interfaces;

interface MediaLinkInterface
{
    /**
     * @param array<mixed> $data
     */
    public function newMediaLink(array $data): void;
}