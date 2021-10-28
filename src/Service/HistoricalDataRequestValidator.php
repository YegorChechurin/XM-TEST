<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\HistoricalDataRequest;
use App\Exception\InvalidHistoricalDataRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HistoricalDataRequestValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(HistoricalDataRequest $request): void
    {
        $violations = $this->validator->validate($request);

        if (0 < count($violations)) {
            throw new InvalidHistoricalDataRequest($violations);
        }
    }
}
