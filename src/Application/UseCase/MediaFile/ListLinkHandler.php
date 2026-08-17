<?php

namespace App\Application\UseCase\MediaFile;

use App\Application\DTO\LinkDto;
use App\Application\Validator\DataValidatorInterface;
use App\Domain\Entity\MediaFile;

class ListLinkHandler
{
    public function __construct(
        private readonly DataValidatorInterface $validator,
        private readonly ListMediaFileService $service
    ) {}

    public function handle(ListLinkRequest $request): ?LinkDto
    {
        $this->validator->validate($request);
        $link = $this->service->findLink($request->id);
        if (!$link instanceof MediaFile) {
            return null;
        }
        //return $this->tranformer->transformMovieLinks($link);
        return null;
    }
}
