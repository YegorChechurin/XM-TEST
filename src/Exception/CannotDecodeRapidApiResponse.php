<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class CannotDecodeRapidApiResponse extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cannot decode Rapid Api response');
    }
}
