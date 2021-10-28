<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\CannotDecodeRapidApiResponse;
use App\Service\RapidApiHistoricalDataGetter;
use App\Service\SymfonyRapidApiClient;
use App\ValueObject\HistoricalDataCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RapidApiHistoricalDataGetterTest extends TestCase
{
    public function testFailedQueryExceptionsAreRethrown(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createFailedQueryExceptionsStub());
        $service = new RapidApiHistoricalDataGetter(
            new SymfonyRapidApiClient('apiKey', $httpClient),
            new NullLogger()
        );

        $this->expectException(ExceptionInterface::class);

        $service->getForCompany('companySymbol');
    }

    public function testInvalidRapidApiResponseTriggersException(): void
    {
        $responseContent = '{"invalidKey":"whatever"}';
        $httpClient = new MockHttpClient(new MockResponse($responseContent));
        $service = new RapidApiHistoricalDataGetter(
            new SymfonyRapidApiClient('apiKey', $httpClient),
            new NullLogger()
        );

        $this->expectException(CannotDecodeRapidApiResponse::class);

        $service->getForCompany('companySymbol');
    }

    /**
     * @dataProvider validRapidApiResponseProvider
     */
     public function testValidRapidApiResponseIsCorrectlyHandled(
         string $responseContent,
         int $expectedNumberOfHistoricalData
     ): void {
         $httpClient = new MockHttpClient(new MockResponse($responseContent));
         $service = new RapidApiHistoricalDataGetter(
             new SymfonyRapidApiClient('apiKey', $httpClient),
             new NullLogger()
         );

         $historicalData = $service->getForCompany('companySymbol');

         $this->assertInstanceOf(HistoricalDataCollection::class, $historicalData);
         $this->assertCount($expectedNumberOfHistoricalData, $historicalData);
     }

     public function validRapidApiResponseProvider(): array
     {
         return [
             [
                 'responseContent' => '{"prices":[{"date":10,"open":10.1,"high":10.1,"low":10.1,"close":10.1,"volume":10,"adjclose":10.1}]}',
                 'expectedNumberOfHistoricalData' => 1,
             ],
             [
                 'responseContent' => '{"prices":[{"date":10,"open":10.1,"high":10.1,"low":10.1,"close":10.1,"volume":10,"adjclose":10.1}, {"amount":0.124,"date":10,"type":"DIVIDEND","data":0.124}]}',
                 'expectedNumberOfHistoricalData' => 1,
             ],
         ];
     }

     private function createFailedQueryExceptionsStub(): ExceptionInterface
     {
         return new class extends Exception implements ExceptionInterface {
             public function __toString()
             {
                 return "I'm just a stub";
             }
         };
     }
}
