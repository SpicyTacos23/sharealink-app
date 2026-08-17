<?php

namespace App\Exception;

use Exception;

class UserNotLoggedException extends Exception
{
    public function __construct(
        string $message = "A valid User is required for this action.",
        int $code = 401,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
