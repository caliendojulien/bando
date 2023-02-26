<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Api
{

    private $httpClient;

    public function __construct(
        HttpClientInterface $httpClient
    )
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getSorties(): array
    {

        $response = $this->httpClient->request(
            'GET',
//            'https://localhost:8000/api/sorties.json',
            'https://api.chucknorris.io/jokes/random',
            [
                "verify_peer" => false
            ]
        );
        return $response->toArray();
    }

}