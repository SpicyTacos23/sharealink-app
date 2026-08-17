<?php

namespace App\Application\DTO;

final class UserDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $username
    ) {}
}
