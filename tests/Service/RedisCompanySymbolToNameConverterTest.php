<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\CompanySymbolToNameRepository;
use App\Service\RedisCompanySymbolToNameConverter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RedisCompanySymbolToNameConverterTest extends TestCase
{
    public function testOnlyDefaultRedisRepositoryImplementationIsAccepted(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RedisCompanySymbolToNameConverter($this->createStub(CompanySymbolToNameRepository::class));
    }
}
