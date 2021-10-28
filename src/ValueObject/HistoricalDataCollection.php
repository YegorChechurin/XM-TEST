<?php

declare(strict_types=1);

namespace App\ValueObject;

use Countable;
use Generator;
use IteratorAggregate;

final class HistoricalDataCollection implements IteratorAggregate, Countable
{
    /** @var HistoricalDatum[] */
    private array $historicalData = [];

    public function addDatum(HistoricalDatum $datum): void
    {
        $this->historicalData[] = $datum;
    }

    public function getIterator(): Generator
    {
        yield from $this->historicalData;
    }

    public function isEmpty(): bool
    {
        return 0 === count($this->historicalData);
    }

    public function count(): int
    {
        return count($this->historicalData);
    }
}
