<?php

namespace App\Domain\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum ApiStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';

    public function trans(TranslatorInterface $translator): string
    {
        return $translator->trans("enum.apistatus.{$this->value}");
    }
}
