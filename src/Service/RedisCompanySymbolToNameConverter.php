<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CannotConvertToCompanyName;
use App\Exception\CannotGetCompanyName;
use App\Repository\CompanySymbolToNameRedisRepository;
use App\Repository\CompanySymbolToNameRepository;
use Webmozart\Assert\Assert;

final class RedisCompanySymbolToNameConverter implements CompanySymbolToNameConverter
{
    private CompanySymbolToNameRepository $redisRepo;

    public function __construct(CompanySymbolToNameRepository $redisRepo)
    {
        Assert::isInstanceOf(
            $redisRepo,
            CompanySymbolToNameRedisRepository::class,
            sprintf('Only default redis implementation of %s is accepted', CompanySymbolToNameRepository::class)
        );

        $this->redisRepo = $redisRepo;
    }

    public function convert(string $companySymbol): string
    {
        try {
            $companyName = $this->redisRepo->getCompanyNameBySymbol($companySymbol);
        } catch (CannotGetCompanyName $e) {
            throw CannotConvertToCompanyName::forCompanySymbol($companySymbol);
        }

        return $companyName;
    }
}
