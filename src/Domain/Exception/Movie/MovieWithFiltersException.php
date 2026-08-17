<?php

namespace App\Domain\Exception\Movie;

use Exception;

class MovieWithFiltersException extends Exception
{
    public function __construct(
        string $message = "Something went wrong trying to get Movie With Filters.",
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
