<?php

namespace App\Interfaces;

interface ModifyUserDataInterface
{
    public function changeProfileImage(string $username, string $image): void;
}