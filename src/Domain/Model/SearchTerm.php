<?php

namespace App\Domain\Model;

use App\Domain\Exception\InvalidSearchTermException;

final class SearchTerm
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 100;

    public function __construct(
        private readonly string $raw
    ) {}

    public static function fromString(string $input): self
    {
        $trimmed = trim(strip_tags($input));
        $collapsed = preg_replace('/\s+/', ' ', $trimmed) ?? '';

        if (mb_strlen($collapsed) < self::MIN_LENGTH) {
            throw InvalidSearchTermException::tooShort(self::MIN_LENGTH);
        }
        if (mb_strlen($collapsed) > self::MAX_LENGTH) {
            throw InvalidSearchTermException::tooLong(self::MAX_LENGTH);
        }

        return new self(mb_strtolower($collapsed));
    }

    public function value(): string
    {
        return $this->raw;
    }

    public function cacheKey(): string
    {
        return 'search:media:' . $this->fromString($this->raw)->value();
    }
}
