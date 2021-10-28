<?php

declare(strict_types=1);

namespace App\Exception;

use InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidHistoricalDataRequest extends InvalidArgumentException
{
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        foreach ($violations as $err) {
            $errors[] = $err->getPropertyPath() . ': ' . $err->getMessage();
        }

        parent::__construct(implode("\n", $errors));
    }
}
