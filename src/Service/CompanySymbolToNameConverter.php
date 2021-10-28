<?php

declare(strict_types=1);

namespace App\Service;

interface CompanySymbolToNameConverter
{
    public function convert(string $companySymbol): string;
}
