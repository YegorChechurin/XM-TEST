<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\HistoricalDataCollection;
use DateTimeImmutable;

final class HistoricalDataFilter
{
    public function filterByDateRange(
        HistoricalDataCollection $historicalData,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): HistoricalDataCollection {
        $filteredData = new HistoricalDataCollection();
        foreach ($historicalData as $datum) {
            if ($datum->getDate() >= $startDate && $datum->getDate() <= $endDate) {
                $filteredData->addDatum($datum);
            }
        }

        return $filteredData;
    }
}
