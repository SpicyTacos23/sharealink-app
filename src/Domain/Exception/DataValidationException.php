<?php

namespace App\Domain\Exception;

use RuntimeException;

class DataValidationException extends RuntimeException
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        private array $errors // ['field' => 'message', ...]
    ) {
        parent::__construct('Validation failed');
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
