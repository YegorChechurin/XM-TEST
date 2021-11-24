<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\CannotConvertToCompanyName;
use App\Service\CompanySymbolToNameConverter;
use App\Service\DefaultCompanySymbolToNameConverter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DefaultCompanySymbolToNameConverterTest extends TestCase
{
    public function testEmptyConvertersChainTriggersException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DefaultCompanySymbolToNameConverter([]);
    }

    public function testOnlyConvertsImplementingInterfaceAreAccepted(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DefaultCompanySymbolToNameConverter(
            [$this->createStub(CompanySymbolToNameConverter::class), new stdClass()]
        );
    }

    public function testExceptionIsThrownWhenAllConvertersInTheChainFail(): void
    {
        $this->expectException(CannotConvertToCompanyName::class);

        $converter = new DefaultCompanySymbolToNameConverter(
            [
                $this->getFailingCompanySymbolToNameConverterStub(),
                $this->getFailingCompanySymbolToNameConverterStub(),
            ]
        );
        $converter->convert('AAA');
    }

    public function testResultOfTheFirstSucceedingConverterIsReturned(): void
    {
        $firstResult = 'first_result';
        $secondResult = 'second_result';

        $converter = new DefaultCompanySymbolToNameConverter(
            [
                $this->getFailingCompanySymbolToNameConverterStub(),
                $this->getWorkingCompanySymbolToNameConverterStub($firstResult),
                $this->getWorkingCompanySymbolToNameConverterStub($secondResult),
            ]
        );

        $this->assertSame($firstResult, $converter->convert('AAA'));
    }

    private function getFailingCompanySymbolToNameConverterStub(): CompanySymbolToNameConverter
    {
        $stub = $this->createStub(CompanySymbolToNameConverter::class);
        $stub->method('convert')
            ->willThrowException(CannotConvertToCompanyName::forCompanySymbol('AAA'));

        return $stub;
    }

    private function getWorkingCompanySymbolToNameConverterStub(
        string $resultToReturn
    ): CompanySymbolToNameConverter {
        $stub = $this->createStub(CompanySymbolToNameConverter::class);
        $stub->method('convert')->willReturn($resultToReturn);

        return $stub;
    }
}
