<?php

namespace App\Domain\Exception\Person;

use Exception;

class PersonFilmographyException extends Exception
{
    public function __construct(
        string $message = "Something went wrong trying to get Person Filmography.",
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
