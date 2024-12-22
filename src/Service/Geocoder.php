<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Geocoder
{
    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct( HttpClientInterface $httpClient)
    {
        // $apiKey = "30f616eb846e4f4ca5339ed7aebbd0cf";
        // $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function getCoordinates(string $address): ?array
    {
        $response = $this->httpClient->request('GET', 'https://api.opencagedata.com/geocode/v1/json', [
            'query' => [
                'q' => $address,
                'key' => "30f616eb846e4f4ca5339ed7aebbd0cf",
                'limit' => 1,
            ],
        ]);

        $data = $response->toArray();

        if (!isset($data['results'][0])) {
            return null;
        }

        $location = $data['results'][0]['geometry'];

        return [
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
        ];
    }
}
