<?php

namespace App\Application\UseCase\TmdbApi\TvShow\LoadShowEpisodes;

use App\Application\DataTransformer\TMDB\ShowDataTransformerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\ApiCaller\Tmdb\TmdbApiCallerInterface;
use App\Domain\Exception\Show\ShowEpisodesException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class LoadShowEpisodesHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly TmdbApiCallerInterface $apiCaller,
        private readonly ShowDataTransformerInterface $dataTransformer,
    ) {}

    /**
     * @throws ShowEpisodesException
     * @return array<mixed>
     */
    public function handle(LoadShowEpisodesRequest $request): array
    {
        //Validate request
        $this->validator->validate($request);

        //Load data
        try {
            $seasons = $this->apiCaller->getShowDetails($request->id);
            $episodes = $this->apiCaller->getShowEpisodes($request->id, $request->seasonNumber);
        } catch (RuntimeException $apiEx) {
            $this->logger->error($apiEx->getMessage());
            throw new ShowEpisodesException();
        }

        //Remove unnecessary data
        foreach ($episodes['episodes'] as &$episode) {
            unset($episode['crew'], $episode['guest_stars']);
        }

        //Model data
        $season = $this->dataTransformer->transformShowSeasons($episodes);

        //Return
        return [
            'season' => $season,
            'numberOfSeasons' => $seasons['number_of_seasons']
        ];
    }
}
