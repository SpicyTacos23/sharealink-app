<?php

namespace App\Enum;

enum MediaType: string
{
    case MOVIES = 'movies';
    case SHOWS = 'shows';
    case PERSONS = 'persons';
}