<?php

namespace App\Domain\Exception;

use Exception;

class JwtValidationException extends Exception
{
    public function __construct(
        string $message = "Somethign went wrong. We'll try to fix it as soon as possible",
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
