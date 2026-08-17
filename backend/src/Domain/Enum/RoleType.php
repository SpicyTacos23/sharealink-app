<?php

namespace App\Domain\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum RoleType: string 
{
    case ACTOR = 'actor';
    case DIRECTOR = 'director';
    case MUSICIAN = 'musician';

   public function trans(TranslatorInterface $translator): string
   {
        return $translator->trans("enum.roletype.{$this->value}");
   }
}