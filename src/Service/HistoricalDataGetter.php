<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\HistoricalDataCollection;

interface HistoricalDataGetter
{
    public function getForCompany(string $companySymbol): HistoricalDataCollection;
}
