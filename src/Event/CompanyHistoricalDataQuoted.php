<?php

declare(strict_types=1);

namespace App\Event;

use DateTimeImmutable;
use Symfony\Contracts\EventDispatcher\Event;

final class CompanyHistoricalDataQuoted extends Event
{
    private string $companySymbol;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;
    private string $email;

    public function __construct(string $companySymbol, DateTimeImmutable $startDate, DateTimeImmutable $endDate, string $email)
    {
        $this->companySymbol = $companySymbol;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->email = $email;
    }

    public function getCompanySymbol(): string
    {
        return $this->companySymbol;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
