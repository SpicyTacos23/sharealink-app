<?php

namespace App\Application\JwtValidator;

use App\Domain\Entity\User;

interface JwtValidatorInterface
{
    public function validate(string $token): User;
}