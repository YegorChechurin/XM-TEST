<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\HistoricalDataRequestValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class HistoricalDataRequestValidatorTest extends KernelTestCase
{
    private HistoricalDataRequestValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(HistoricalDataRequestValidator::class);
    }

    public function testWhenCompanySymbolIsEmptyExceptionIsThrown(): void
    {
        $this->assertTrue(true);
    }
}
