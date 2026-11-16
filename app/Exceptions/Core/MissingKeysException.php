<?php

declare(strict_types=1);

namespace App\Exceptions\Core;

use RuntimeException;

final class MissingKeysException extends RuntimeException
{
    public static function withMissingKeys(array $missingKeys): self
    {
        return new self('Missing keys: ' . implode(', ', $missingKeys));
    }
}
