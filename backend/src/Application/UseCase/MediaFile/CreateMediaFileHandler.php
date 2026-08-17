<?php

namespace App\Application\UseCase\MediaFile;

use App\Application\Validator\DataValidatorInterface;
use App\Domain\Entity\MediaFile;
use App\Domain\Entity\Movie;
use App\Domain\Enum\MediaType;
use App\Domain\Repository\MediaFileRepositoryInterface;
use App\Domain\Repository\MovieRepositoryInterface;

class CreateMediaFileHandler
{
    public function __construct(
        private readonly DataValidatorInterface $validator,
        private readonly MediaFileRepositoryInterface $mediaFileRepository,
        private readonly MovieRepositoryInterface $movieRepository
    ) {}

    public function handle(CreateMediaFileRequest $request): void
    {
        $this->validator->validate($request);

        //Create local movie if not exists
        $movie = $this->movieRepository->findOneBy(['movieId' => $request->movie]);
        if (!$movie instanceof Movie) {
            $movie = new Movie();
            $movie->setMovieId($request->movie)
                ->setTitle($request->movieTitle)
                ->setMovieImage($request->movieImage);
        }

        /* @TODO: Check how can we validate if link exists. Use Subscriber */
        //Create Media
        $media = new MediaFile();
        $media->setMediaType(MediaType::MOVIE)
            ->setServer($request->server)
            ->setQuality($request->quality)
            ->setLanguage($request->language)
            ->setUser($request->user)
            ->setLink($request->link)
            ->setIframeLink($request->iframe);

        $this->mediaFileRepository->saveMovieAndMedia($movie, $media);
    }
}
