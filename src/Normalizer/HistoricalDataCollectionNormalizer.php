<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\ValueObject\HistoricalDataCollection;

final class HistoricalDataCollectionNormalizer
{
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @return array<int, array<string, string|float|int>>
     */
    public function normalize(HistoricalDataCollection $historicalDataCollection): array
    {
        $normalizedData = [];

        foreach ($historicalDataCollection as $datum) {
            $normalizedData[] = [
                'date' => $datum->getDate()->format(self::DATE_FORMAT),
                'open' => $datum->getOpen(),
                'high' => $datum->getHigh(),
                'low' => $datum->getLow(),
                'close' => $datum->getClose(),
                'volume' => $datum->getVolume(),
            ];
        }

        return $normalizedData;
    }
}
