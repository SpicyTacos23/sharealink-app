<?php

namespace App\Service;

use App\Exception\UploadMediaLinkException;
use App\Interfaces\MediaLinkInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UploadMediaLink implements MediaLinkInterface
{
    const UPLOAD_MEDIA_LINK = 'http://127.0.0.1:8000/api/v1/media-file/create';

    public function __construct(private readonly HttpClientInterface $client) {}

    public function newMediaLink(array $data): void
    {
        $url = self::UPLOAD_MEDIA_LINK;
        $response = $this->client->request(
            'POST',
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $data['userToken'],
                    'Accept'        => 'application/json',
                ],
                'json' => $data
            ]
        );
        $result = $response->getContent(false);
        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            throw new UploadMediaLinkException($result, Response::HTTP_BAD_REQUEST);
        }
        
    }
}
