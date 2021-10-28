<?php

declare(strict_types=1);

namespace App\ValueObject;

use DateTimeImmutable;

final class HistoricalDatum
{
    private DateTimeImmutable $date;
    private float $open;
    private float $high;
    private float $low;
    private float $close;
    private int $volume;

    public function __construct(
        DateTimeImmutable $date,
        float $open,
        float $high,
        float $low,
        float $close,
        int $volume
    ) {
        $this->date = $date;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
        $this->volume = $volume;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }
}
