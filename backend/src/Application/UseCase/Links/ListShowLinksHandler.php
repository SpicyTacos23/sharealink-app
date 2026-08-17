<?php

namespace App\Application\UseCase\Links;

use App\Application\DataTransformer\LinkDataTransformerInterface;
use App\Application\Validator\DataValidatorInterface;
use App\Domain\Entity\Show;
use App\Domain\Repository\ShowRepositoryInterface;

class ListShowLinksHandler
{
    public function __construct(
        private readonly DataValidatorInterface $validator,
        private readonly ShowRepositoryInterface $showRepository
    ) {}

    /**
     * @return array<mixed>
     */
    public function handle(ListShowLinksRequest $request): array
    {
        //Validate movie
        $this->validator->validate($request);
        $show = $this->showRepository->findOneBy(['movieId' => $request->id]);
       
        if (!$show instanceof Show) {
            return [];
        }

        //No tengo claro cómo va a funcionar lo de las series. 
        return[];

        /*
        $links = $show->getMediaFileId()->toArray();
        if (empty($links)) {
            return [];
        }

        $dtos = [];
        foreach ($links as $link) {
            $dtos[] = $this->transformer->transformMovieLinks($link);
        }

        //Return content
        return $dtos;
        */
    }
}
