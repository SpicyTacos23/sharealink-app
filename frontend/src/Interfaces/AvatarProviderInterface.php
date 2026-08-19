<?php

namespace App\Interfaces;

interface AvatarProviderInterface
{
    /**
     * @return array<mixed>
     */
    public function getAllAvatars(string $basePath): array;
}