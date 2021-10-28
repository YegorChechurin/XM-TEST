<?php

declare(strict_types=1);

namespace App\Service;

interface DatahubClient
{
    /**
     * @return  array<int, array<string, string|float>>
     */
    public function getCompanyMetaDataList(): array;
}