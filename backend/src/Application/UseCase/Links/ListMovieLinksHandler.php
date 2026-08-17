<?php

namespace App\Application\UseCase\Links;

use App\Domain\Entity\Movie;
use App\Domain\Repository\MovieRepositoryInterface;
use App\Application\Validator\DataValidatorInterface;
use App\Application\DataTransformer\LinkDataTransformerInterface;

class ListMovieLinksHandler
{
    public function __construct(
        private readonly DataValidatorInterface $validator,
        private readonly MovieRepositoryInterface $movieRepository,
        private readonly LinkDataTransformerInterface $transformer,
    ) {}

    /**
     * @return array<mixed>
     */
    public function handle(ListMovieLinksRequest $request): array
    {
        $this->validator->validate($request);

        $movie = $this->movieRepository->findOneBy(['movieId' => $request->id]);
        if (!$movie instanceof Movie) {
            return [];
        }

        $links = [];
        foreach ($movie->getMediaFileId()->toArray() as $link) {
            $dto = $this->transformer->transformLink($link);
            $links[] = $dto;
        }

        return $links;
    }
}
