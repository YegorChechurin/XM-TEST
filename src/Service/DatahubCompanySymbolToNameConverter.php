<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CannotConvertToCompanyName;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class DatahubCompanySymbolToNameConverter implements CompanySymbolToNameConverter
{
    private DatahubClient $client;
    private LoggerInterface $logger;

    public function __construct(DatahubClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function convert(string $companySymbol): string
    {
        try {
            $metaData = $this->client->getCompanyMetaDataList();
            if (0 === count($metaData)) {
                throw CannotConvertToCompanyName::forCompanySymbol($companySymbol);
            }

            $companyName = $this->getCompanyName($companySymbol, $metaData);
        } catch (ExceptionInterface | CannotConvertToCompanyName $e) {
            $this->logger->error(
                'Cannot convert company symbol to company name by metadata list from Datahub',
                [
                    'companySymbol' => $companySymbol,
                    'exception' => $e,
                ]
            );

            throw $e;
        }

        return $companyName;
    }

    /**
     * @param array<int, array<string, string|float>> $metaData
     */
    private function getCompanyName(string $companySymbol, array $metaData): string
    {
        foreach ($metaData as $datum) {
            if (empty($datum['Company Name']) || empty($datum['Symbol'])) {
                continue;
            }

            if ($companySymbol === $datum['Symbol']) {
                return $datum['Company Name'];
            }
        }

        throw CannotConvertToCompanyName::forCompanySymbol($companySymbol);
    }
}
