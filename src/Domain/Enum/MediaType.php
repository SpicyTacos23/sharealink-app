<?php

namespace App\Domain\Enum;

enum MediaType: string
{ 
    case MOVIE = 'movie';
    case SHOW = 'tvSeries';
    case EPISODE = 'episode';
    case UNKNOWN = 'unknown';
}