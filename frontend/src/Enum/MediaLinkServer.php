<?php

namespace App\Enum;

enum MediaLinkServer: string
{
    case VOE = 'VOE';
    case FILEMOON = 'FILEMOON';
    case DOODSTREAM = 'DOODSTREAM';
    case STREAMWISH = 'STREAMWISH';
    case LULUSTREAM = 'LULUSTREAM';
    case VIDHIDE = 'VIDHIDE';
    case POWVIDEO = 'POWVIDEO';
    case VIDMOLY = 'VIDMOLY';
    case NETU = 'NETU';
    case STREAMTAPE = 'STREAMTAPE';
    case KRAKENFILES = 'KRAKENFILES';
    case OTHERS = 'ALTERNATIVO';
}