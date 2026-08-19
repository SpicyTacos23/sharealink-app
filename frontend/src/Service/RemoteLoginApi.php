<?php

namespace App\Service;

use App\Interfaces\RemoteLoginApiInterface;
use Symfony\Component\HttpFoundation\Cookie;
use App\Exception\LoginErrorException;

class RemoteLoginApi implements RemoteLoginApiInterface
{
    /**
     * @param array<mixed> $data
     */
    public function getToken(array $data): Cookie
    {
        $response = $this->callRemoteLogin($data['email'], $data['password']);

        $jwt = $response['token'] ?? null;
        if (is_null($jwt)) {
            throw new LoginErrorException("Invalid credentials provided.");
        }

        return Cookie::create('userAuthToken')
            ->withValue($jwt)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('strict')
            ->withPath('/');
    }


    /**
     * @return array<mixed>
     */
    private function callRemoteLogin(string $email, string $password): array
    {
        $client = new \Symfony\Component\HttpClient\HttpClient();

        $http = \Symfony\Component\HttpClient\HttpClient::create();

        $response = $http->request('POST', $_ENV['BACKEND_LOGIN_URL'], [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return ['success' => false];
        }

        $data = $response->toArray();

        return [
            'success' => true,
            'token' => $data['token'],
        ];
    }
}
