<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SymfonyRapidApiClient implements RapidApiClient
{
    private const HOST = 'https://yh-finance.p.rapidapi.com';
    private const X_HEADER = 'X-Rapidapi-Key';
    private const TIMEOUT = 5;

    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        string $rapidApiKey,
        HttpClientInterface $httpClient
    ) {
        $this->apiKey = $rapidApiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritDoc
     */
    public function get(string $endpointUrl, array $queryStringParams = []): array
    {
        return $this->httpClient
            ->request(
                Request::METHOD_GET,
                self::HOST . $endpointUrl,
                [
                    'query' => $queryStringParams,
                    'headers' => [self::X_HEADER => $this->apiKey],
                    'timeout' => self::TIMEOUT,
                ]
            )
            ->toArray();
    }
}
