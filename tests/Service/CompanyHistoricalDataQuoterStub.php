<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\CompanyHistoricalDataQuoter;
use App\ValueObject\HistoricalDataCollection;
use App\ValueObject\HistoricalDatum;
use DateTimeImmutable;

final class CompanyHistoricalDataQuoterStub implements CompanyHistoricalDataQuoter
{
    public function quoteForDateRange(
        string $companySymbol,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $quoteRequesterEmail
    ): HistoricalDataCollection {
        $data = new HistoricalDataCollection();
        $data->addDatum(
            new HistoricalDatum(
                new DateTimeImmutable(),
                1.1,
                1.1,
                1.1,
                1.1,
                1
            )
        );

        return $data;
    }
}
