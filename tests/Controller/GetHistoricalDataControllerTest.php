<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetHistoricalDataControllerTest extends WebTestCase
{
    /**
     * @dataProvider requestWithMissingParamsProvider
     */
    public function testMissingParamsProduceBadRequestResponse(array $requestParams): void
    {
        $this->assertSame(
            Response::HTTP_BAD_REQUEST,
            $this->getControllerResponse($requestParams)->getStatusCode()
        );
    }

    public function requestWithMissingParamsProvider(): array
    {
        return [
            'no_company_symbol' => [
                [
                    'startDate' => '2021-10-05',
                    'endDate' => '2021-10-12',
                    'email' => 'email@email.email',
                ]
            ],
            'no_start_date' => [
                [
                    'companySymbol' => 'ABC',
                    'endDate' => '2021-10-12',
                    'email' => 'email@email.email',
                ]
            ],
            'no_end_date' => [
                [
                    'companySymbol' => 'ABC',
                    'startDate' => '2021-10-05',
                    'email' => 'email@email.email',
                ]
            ],
            'no_email' => [
                [
                    'companySymbol' => 'ABC',
                    'startDate' => '2021-10-05',
                    'endDate' => '2021-10-12',
                ]
            ],
        ];
    }

    public function testInvalidCompanySymbolProducesBadRequestResponse(): void
    {
        $response = $this->getControllerResponse(
            [
                'companySymbol' => 'invalidCompanySymbol',
                'startDate' => '2021-10-05',
                'endDate' => '2021-10-05',
                'email' => 'email@email.email',
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testInvalidEmailProducesBadRequestResponse(string $invalidEmail): void
    {
        $response = $this->getControllerResponse(
            [
                'companySymbol' => 'AAA',
                'startDate' => '2021-10-05',
                'endDate' => '2021-10-05',
                'email' => $invalidEmail,
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function invalidEmailProvider(): array
    {
        return [['invalidEmail'], ['invalidEmail@'], ['invalidEmail@host'],];
    }

    /**
     * @dataProvider requestWithInvalidDatesProvider
     */
    public function testInvalidDatesProduceBadRequestResponse(array $requestWithInvalidDates): void
    {
        $this->assertSame(
            Response::HTTP_BAD_REQUEST,
            $this->getControllerResponse($requestWithInvalidDates)->getStatusCode()
        );
    }

    public function requestWithInvalidDatesProvider(): array
    {
        return [
            'invalid_start_date_format' => [
                [
                    'companySymbol' => 'AAA',
                    'startDate' => '2021/10/05',
                    'endDate' => '2021-10-05',
                    'email' => 'email@email.email',
                ]
            ],
            'invalid_end_date_format' => [
                [
                    'companySymbol' => 'AAA',
                    'startDate' => '2021-10-05',
                    'endDate' => '2021/10/05',
                    'email' => 'email@email.email',
                ]
            ],
            'start_date_greater_than_end_date' => [
                [
                    'companySymbol' => 'AAA',
                    'startDate' => (new DateTimeImmutable('-1 day'))->format('Y-m-d'),
                    'endDate' => (new DateTimeImmutable('-7 day'))->format('Y-m-d'),
                    'email' => 'email@email.email',
                ]
            ],
            'end_date_greater_than_today' => [
                [
                    'companySymbol' => 'AAA',
                    'startDate' => (new DateTimeImmutable('-1 day'))->format('Y-m-d'),
                    'endDate' => (new DateTimeImmutable('+1 day'))->format('Y-m-d'),
                    'email' => 'email@email.email',
                ]
            ],
            'both_dates_greater_than_today' => [
                [
                    'companySymbol' => 'AAA',
                    'startDate' => (new DateTimeImmutable('+1 day'))->format('Y-m-d'),
                    'endDate' => (new DateTimeImmutable('+1 day'))->format('Y-m-d'),
                    'email' => 'email@email.email',
                ]
            ],
        ];
    }

    public function testValidRequestProducesSuccessfulResponse(): void
    {
        $response = $this->getControllerResponse(
            [
                'companySymbol' => 'AAA',
                'startDate' => '2021-10-05',
                'endDate' => '2021-10-05',
                'email' => 'email@email.email',
            ]
        );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    private function getControllerResponse(array $requestParams): Response
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/historical-data', $requestParams);

        return $client->getResponse();
    }
}
