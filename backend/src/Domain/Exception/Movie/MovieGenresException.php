<?php

namespace App\Domain\Exception\Movie;

use Exception;

class MovieGenresException extends Exception
{
    public function __construct(
        string $message = "Something went wrong trying to get Movie Genres.",
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
