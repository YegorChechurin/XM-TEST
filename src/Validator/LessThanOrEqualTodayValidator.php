<?php

declare(strict_types=1);

namespace App\Validator;

use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class LessThanOrEqualTodayValidator extends ConstraintValidator
{
    private const DATE_FORMAT = 'Y-m-d';

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LessThanOrEqualToday) {
            throw new UnexpectedTypeException($constraint, LessThanOrEqualToday::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $date = new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new UnexpectedValueException($value, 'valid date string');
        }

        if ($date->format(self::DATE_FORMAT) > (new DateTimeImmutable())->format(self::DATE_FORMAT)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ date }}', $value)
                ->addViolation();
        }
    }
}
