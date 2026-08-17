<?php

namespace App\Application\DataTransformer;

use App\Application\DTO\LinkDto;
use App\Domain\Entity\MediaFile;

interface LinkDataTransformerInterface
{
    public function transformLink(MediaFile $media): LinkDto;
}