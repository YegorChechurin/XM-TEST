<?php

declare(strict_types=1);

namespace App\Service;

interface RapidApiClient
{
    /**
     * @param array<string, mixed> $queryStringParams
     *
     * @return array<int, array<string, int|float|string>>
     */
    public function get(string $endpointUrl, array $queryStringParams): array;
}