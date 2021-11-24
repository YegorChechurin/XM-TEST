<?php

namespace App\Repository;

interface CompanySymbolToNameRepository
{
    public function saveCompanySymbolToName(
        string $companySymbol,
        string $companyName
    ): void;

    public function getCompanyNameBySymbol(string $companySymbol): string;
}