<?php

namespace App\Provider;

use App\Interfaces\AvatarProviderInterface;

final class AvatarProvider implements AvatarProviderInterface
{
    public function getAllAvatars(string $basePath): array
    {
        $avatarsDir = $basePath . '/public/build/images/avatars';
        $files = glob($avatarsDir . '/*.{png,jpg,jpeg,svg}', GLOB_BRACE);

        return array_map(function ($file) {
            return 'build/images/avatars/' . basename($file);
        }, $files ?: []);
    }
}