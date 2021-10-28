<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\CannotConvertToCompanyName;
use App\Service\DatahubClient;
use App\Service\DatahubCompanySymbolToNameConverter;
use App\Service\SymfonyDatahubClient;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DatahubCompanySymbolToNameConverterTest extends TestCase
{
    public function testFailedQueryExceptionsAreRethrown(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('request')->willThrowException($this->createFailedQueryExceptionsStub());
        $service = new DatahubCompanySymbolToNameConverter(new SymfonyDatahubClient($httpClient), new NullLogger());

        $this->expectException(ExceptionInterface::class);

        $service->convert('whatever');
    }

    /**
     * @dataProvider invalidDatahubResponseProvider
     */
    public function testInvalidDatahubResponseTriggersException(array $invalidResponse): void
    {
        $datahubClient = $this->createStub(DatahubClient::class);
        $datahubClient->method('getCompanyMetaDataList')->willReturn($invalidResponse);
        $service = new DatahubCompanySymbolToNameConverter($datahubClient, new NullLogger());

        $this->expectException(CannotConvertToCompanyName::class);

        $service->convert('whatever');
    }

    public function invalidDatahubResponseProvider(): array
    {
        return [
            [[]],
            [['invalidKey' => 'whatever']],
        ];
    }

    public function testValidDatahubResponseIsCorrectlyHandled(): void
    {
        $companySymbol = 'ABC';
        $companyName = 'Expected Name';

        $responseContent = sprintf(
            '[{"Symbol":"%s","Company Name":"%s", "whatever":"whatever"}, {"whatever":"whatever"}]',
            $companySymbol,
            $companyName
        );
        $httpClient = new MockHttpClient(new MockResponse($responseContent));
        $service = new DatahubCompanySymbolToNameConverter(new SymfonyDatahubClient($httpClient), new NullLogger());

        $this->assertSame($companyName, $service->convert($companySymbol));
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
