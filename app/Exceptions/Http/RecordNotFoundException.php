<?php

declare(strict_types=1);

namespace App\Exceptions\Http;

/**
 * @template TModel of object
 */
class RecordNotFoundException extends NotFoundException
{
    private const STR_ERROR_FORMAT = 'Record not found for class "%s" with ID "%s"';

    /**
     * @param class-string<TModel> $class
     * @param mixed $value
     */
    public function __construct(string $class, mixed $value)
    {
        parent::__construct(sprintf(self::STR_ERROR_FORMAT, str($class), str($value)));
    }
}
