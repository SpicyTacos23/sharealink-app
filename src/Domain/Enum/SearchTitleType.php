<?php

namespace App\Domain\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum SearchTitleType: string
{
    case MOVIE = 'movie';
    case TV_SERIES = 'tv_series';
    case TV_MINI_SERIES = 'tv_mini_series';
    case TV_MOVIE = 'tv_movie';
    case SHORT = 'short';
    case VIDEO = 'video';
    case VIDEO_GAME = 'video_game';

    public function trans(TranslatorInterface $translator): string
   {
        return $translator->trans("enum.searchElementType.{$this->value}");
   }
}