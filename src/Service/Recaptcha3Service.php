<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Recaptcha3Service
{
    private $client;
    private $secretKey;

    public function __construct(HttpClientInterface $client, string $secretKey)
    {
        $this->client = $client;
        $this->secretKey = $secretKey;
    }

    public function verify(string $token, string $action): bool
    {
        $response = $this->client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ],
        ]);

        $data = $response->toArray();

        return $data['success'] && $data['action'] === $action;
    }
}
