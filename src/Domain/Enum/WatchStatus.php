<?php

namespace App\Domain\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum WatchStatus: string 
{
    case PENDING = 'pending';
    case WATCHING = 'watching';
    case WATCHED = 'watched';
    

   public function trans(TranslatorInterface $translator): string
   {
        return $translator->trans("enum.watchstatus.{$this->value}");
   }
}