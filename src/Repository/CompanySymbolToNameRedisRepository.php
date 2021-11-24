<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\CannotGetCompanyName;
use Redis;

final class CompanySymbolToNameRedisRepository implements CompanySymbolToNameRepository
{
    private const REDIS_KEY = 'company-symbol-to-company-name';

    private Redis $redisClient;

    public function __construct(Redis $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function saveCompanySymbolToName(string $companySymbol, string $companyName): void
    {
        $this->redisClient->hSet(self::REDIS_KEY, $companySymbol, $companyName);
    }

    public function getCompanyNameBySymbol(string $companySymbol): string
    {
        $companyName = $this->redisClient->hGet(self::REDIS_KEY, $companySymbol);
        if (! $companyName) {
            throw CannotGetCompanyName::byCompanySymbol($companySymbol);
        }

        return $companyName;
    }
}
