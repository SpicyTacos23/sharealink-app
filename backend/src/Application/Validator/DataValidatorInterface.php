<?php

namespace App\Application\Validator;

interface DataValidatorInterface
{
    public function validate(object $dto): void;
}
