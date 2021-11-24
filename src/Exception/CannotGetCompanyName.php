<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class CannotGetCompanyName extends RuntimeException
{
    public static function byCompanySymbol(string $companySymbol): self
    {
        return new self(
            sprintf(
                'Cannot get company name by company symbol "%s"',
                $companySymbol
            )
        );
    }
}
