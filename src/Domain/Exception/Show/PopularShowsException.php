<?php

namespace App\Domain\Exception\Show;

use Exception;

class PopularShowsException extends Exception
{
    public function __construct(
        string $message = "Something went wrong trying to get Popular Shows.",
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
