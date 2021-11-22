<?php

namespace App\Service;

use App\ValueObject\HistoricalDataCollection;
use DateTimeImmutable;

interface CompanyHistoricalDataQuoter
{
    public function quoteForDateRange(
        string $companySymbol,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $quoteRequesterEmail
    ): HistoricalDataCollection;
}