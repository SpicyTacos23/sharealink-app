<?php

namespace App\Exception;

use Exception;

class LoginErrorException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
