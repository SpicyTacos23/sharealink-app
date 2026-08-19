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
        $link = $this->apiData->getLinkDetails($id, $this->jwtValidator->getToken() ?? '');
        $content = $link->getContent();

        if ($content === false || $content === '') {
            throw new MediaStreamException("Invalid link provided!");
        }

        $decodedContent = json_decode($content, true);
        $decodedContent = is_array($decodedContent) ? $decodedContent : [];

        if (empty($decodedContent['link'])) {
            throw new MediaStreamException("Media has no Link available!");
        }

        $responseContent = [
            "link" => $decodedContent['link'],
            "iframe" => $decodedContent['iframeLink'] ?? null
        ];


        $encoded = json_encode($responseContent);
        if ($encoded === false) {
            $encoded = null;
        }
        return new Response($encoded, Response::HTTP_OK);
    }
}
