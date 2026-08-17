<?php

namespace App\Domain\Exception;

final class InvalidSearchTermException extends \DomainException
{
    public static function tooShort(int $min): self
    {
        return new self(sprintf('El término de búsqueda debe tener al menos %d caracteres.', $min));
    }

    public static function tooLong(int $max): self
    {
        return new self(sprintf('El término de búsqueda no puede superar %d caracteres.', $max));
    }
}