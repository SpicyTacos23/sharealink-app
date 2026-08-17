<?php

namespace App\Interfaces;

interface RemoteUserRepositoryInterface
{
    public function getUserAvatar(string $authToken): string;
    public function getUserUsername(string $authToken): string;
    public function updateUserAvatar(string $avatar, string $authToken): void;
}