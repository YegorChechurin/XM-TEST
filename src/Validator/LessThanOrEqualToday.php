<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class LessThanOrEqualToday extends Constraint
{
    public string $message = 'The date "{{ date }}" must be less than or equal today.';
}
