<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class CannotConvertToCompanyName extends RuntimeException
{
    public static function forCompanySymbol(string $companySymbol): self
    {
        return new self(
            sprintf(
                'Cannot convert to company name for for company symbol "%s"',
                $companySymbol
            )
        );
    }
}
