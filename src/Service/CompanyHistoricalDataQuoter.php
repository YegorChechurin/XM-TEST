<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\CompanyHistoricalDataQuoted;
use App\Exception\CannotFindHistoricalDataWithinRequestedDateRange;
use App\ValueObject\HistoricalDataCollection;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class CompanyHistoricalDataQuoter
{
    private HistoricalDataGetter $dataGetter;
    private HistoricalDataFilter $dataFilter;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        HistoricalDataGetter $dataGetter,
        HistoricalDataFilter $dataFilter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->dataGetter = $dataGetter;
        $this->dataFilter = $dataFilter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function quoteForDateRange(
        string $companySymbol,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $quoteRequesterEmail
    ): HistoricalDataCollection {
        $quote = $this->getQuote($companySymbol, $startDate, $endDate, $quoteRequesterEmail);

        $this->eventDispatcher->dispatch(
            new CompanyHistoricalDataQuoted(
                $companySymbol,
                $startDate,
                $endDate,
                $quoteRequesterEmail
            )
        );

        return $quote;
    }

    private function getQuote(
        string $companySymbol,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $quoteRequesterEmail
    ): HistoricalDataCollection {
        try {
            $data = $this->dataGetter->getForCompany($companySymbol);
            $filteredData = $this->dataFilter->filterByDateRange($data, $startDate, $endDate);
            if (0 === count($filteredData)) {
                throw new CannotFindHistoricalDataWithinRequestedDateRange($companySymbol, $startDate, $endDate);
            }
        } catch (Throwable $e) {
            $this->eventDispatcher->dispatch(
                new CompanyHistoricalDataQuoted(
                    $companySymbol,
                    $startDate,
                    $endDate,
                    $quoteRequesterEmail
                )
            );

            throw $e;
        }

        return $filteredData;
    }
}
