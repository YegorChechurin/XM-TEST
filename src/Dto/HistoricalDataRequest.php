<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator as XmAssert;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class HistoricalDataRequest
{
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z]{3,5}$/", message="Must be from 3 to 5 capital latin letters.")
     */
    private string $companySymbol;

    /**
     * @Assert\NotBlank
     * @Assert\Date
     * @XmAssert\LessThanOrEqualToday
     * @Assert\LessThanOrEqual(propertyPath="endDate")
     */
    private string $startDate;

    /**
     * @Assert\NotBlank
     * @Assert\Date
     * @XmAssert\LessThanOrEqualToday
     */
    private string $endDate;

    /**
     * @Assert\NotBlank
     * @Assert\Email(mode="html5")
     */
    private string $email;

    public function __construct(string $companySymbol, string $startDate, string $endDate, string $email)
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

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getStartDateAsDateTimeImmutable(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->startDate);
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getEndDateAsDateTimeImmutable(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $this->endDate);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
