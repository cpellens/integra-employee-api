<?php

declare(strict_types=1);

namespace App\Exceptions\Http;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public static function withMessage(string $message): static
    {
        return new static($message);
    }
}
