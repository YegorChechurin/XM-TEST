<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CannotDecodeRapidApiResponse;
use App\ValueObject\HistoricalDataCollection;
use App\ValueObject\HistoricalDatum;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class RapidApiHistoricalDataGetter implements HistoricalDataGetter
{
    private const ENDPOINT = '/stock/v3/get-historical-data';
    private const QUERY_PARAM = 'symbol';
    private const RESPONSE_KEY = 'prices';

    private RapidApiClient $client;
    private LoggerInterface $logger;

    public function __construct(RapidApiClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function getForCompany(string $companySymbol): HistoricalDataCollection
    {
        try {
            $response = $this->client->get(self::ENDPOINT, [self::QUERY_PARAM => $companySymbol]);
            $historicalDataArray = $this->decodeResponse($response);
            $historicalDataCollection = $this->assembleHistoricalDataCollection($historicalDataArray);
        } catch (ExceptionInterface $e) {
            $this->logger->error(
                'Cannot query Rapid Api',
                [
                    'companySymbol' => $companySymbol,
                    'endpoint' => self::ENDPOINT,
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (CannotDecodeRapidApiResponse $e) {
            $this->logger->error(
                'Cannot decode Rapid Api response',
                [
                    'companySymbol' => $companySymbol,
                    'endpoint' => self::ENDPOINT,
                    'response' => $response ?? null,
                ]
            );

            throw $e;
        }

        return $historicalDataCollection;
    }

    /**
     * @return array<int, array<string, int|float|string>>
     */
    private function decodeResponse(array $response): array
    {
        if (empty($response[self::RESPONSE_KEY])) {
            throw new CannotDecodeRapidApiResponse();
        }

        return $response[self::RESPONSE_KEY];
    }

    /**
     * @param array<int, array<string, int|float|string>> $historicalDataArray
     */
    private function assembleHistoricalDataCollection(array $historicalDataArray): HistoricalDataCollection
    {
        $historicalDataCollection = new HistoricalDataCollection();
        foreach ($historicalDataArray as $datumArray) {
            if ($this->datumArrayIsEmpty($datumArray)) {
                continue;
            }

            $historicalDataCollection->addDatum(
                new HistoricalDatum(
                    new DateTimeImmutable('@' . $datumArray['date']),
                    $datumArray['open'],
                    $datumArray['high'],
                    $datumArray['low'],
                    $datumArray['close'],
                    $datumArray['volume']
                )
            );
        }

        return $historicalDataCollection;
    }

    private function datumArrayIsEmpty(array $datumArray): bool
    {
        return empty($datumArray['open'])
            || empty($datumArray['high'])
            || empty($datumArray['low'])
            || empty($datumArray['close'])
            || empty($datumArray['volume']);
    }
}
