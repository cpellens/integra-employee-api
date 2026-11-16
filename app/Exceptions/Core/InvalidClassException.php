<?php

declare(strict_types=1);

namespace App\Exceptions\Core;

use InvalidArgumentException;

final class InvalidClassException extends InvalidArgumentException
{
    private const STR_ERROR_FORMAT_EXPECTED = 'Invalid class provided. Expected a subclass of %s, got %s';

    public static function withExpected(string $classNameExpected, string $classNameProvided): self
    {
        return new self(sprintf(self::STR_ERROR_FORMAT_EXPECTED, $classNameExpected, $classNameProvided));
    }
}
