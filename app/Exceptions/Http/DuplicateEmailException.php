<?php

declare(strict_types=1);

namespace App\Exceptions\Http;

use RuntimeException;

final class DuplicateEmailException extends RuntimeException
{
    private const MESSAGE = 'An employee with the email %s already exists.';

    public static function withEmail(string $email): self
    {
        return new self(sprintf(self::MESSAGE, $email));
    }
}
