<?php

namespace App\Domain\Exception\Show;

use Exception;

class ShowGenresException extends Exception
{
    public function __construct(
        string $message = "Something went wrong trying to get Show Genres.",
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
