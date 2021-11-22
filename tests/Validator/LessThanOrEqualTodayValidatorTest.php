<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\LessThanOrEqualToday;
use App\Validator\LessThanOrEqualTodayValidator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LessThanOrEqualTodayValidatorTest extends TestCase
{
    private ExecutionContext $validationContext;
    private LessThanOrEqualTodayValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $this->validationContext = new ExecutionContext(
            $this->createMock(ValidatorInterface::class),
            'test',
            $translator
        );
        $this->validator = new LessThanOrEqualTodayValidator();
        $this->validator->initialize($this->validationContext);
    }

    public function testDateGreaterThanTodayProducesViolation(): void
    {
        $this->validator->validate(
            (new DateTimeImmutable('+1 day'))->format('Y-m-d'),
            new LessThanOrEqualToday()
        );

        $this->assertNotEmpty($this->validationContext->getViolations());
    }

    /**
     * @dataProvider lessThanOrEqualTodayDatesProvider
     */
    public function testDatesLessThanOrEqualTodayPass(): void
    {
        $this->validator->validate(
            (new DateTimeImmutable('-1 day'))->format('Y-m-d'),
            new LessThanOrEqualToday()
        );

        $this->assertEmpty($this->validationContext->getViolations());
    }

    public function lessThanOrEqualTodayDatesProvider(): array
    {
        return [
            [(new DateTimeImmutable('-1 day'))->format('Y-m-d')],
            [(new DateTimeImmutable())->format('Y-m-d')],
        ];
    }
}
