<?php

declare(strict_types=1);

namespace App\Exception;

use DateTimeImmutable;
use RuntimeException;

final class CannotFindHistoricalDataWithinRequestedDateRange extends RuntimeException
{
    private const DATE_FORMAT = 'Y-m-d';

    public function __construct(string $companySymbol, DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        parent::__construct(
            sprintf(
                'Cannot find company %s historical data within requested date range %s : %s',
                $companySymbol,
                $startDate->format(self::DATE_FORMAT),
                $endDate->format(self::DATE_FORMAT)
            )
        );
    }
}
