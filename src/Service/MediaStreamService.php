<?php

namespace App\Service;

use App\Exception\MediaStreamException;
use App\Exception\UserNotLoggedException;
use App\Interfaces\GetApiDataInterface;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\MediaStreamInterface;
use Symfony\Component\HttpFoundation\Response;

class MediaStreamService implements MediaStreamInterface
{

    public function __construct(
        private readonly JwtValidatorInterface $jwtValidator,
        private readonly GetApiDataInterface $apiData
    ) {}

    public function handleMediaStream(int $id): Response
    {
        //Validate User is logged
        $this->jwtValidator->removeAuthToken(new Response());

        if (!$this->jwtValidator->isLoggedIn()) {
            throw new UserNotLoggedException();
        }

        //Validate Content Token is set or user has special permissions
        //.. Use a special service to get this validation
        //@TODO: For now true
        $hasAccess = true;

        //Validate Link
        $link = $this->apiData->getLinkDetails($id, $this->jwtValidator->getToken());
        $content = json_decode($link->getContent(), true);
        if (empty($content)) {
            throw new MediaStreamException("Invalid link provided!");
        } elseif (empty($content['link'])) {
            throw new MediaStreamException("Media has no Link available!");
        }

        $content = [
            "link" => $content['link'],
            "iframe" => $content['iframeLink'] ?? null
        ];
        return new Response(json_encode($content), Response::HTTP_OK);
    }
}
