<?php

namespace App\Interfaces;

interface AvatarProviderInterface
{
    public function getAllAvatars(string $basePath): array;
}