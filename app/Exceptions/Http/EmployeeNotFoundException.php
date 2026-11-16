<?php

declare(strict_types=1);

namespace App\Exceptions\Http;

use App\DataObjects\EmployeeData;

/**
 * @extends RecordNotFoundException<EmployeeData>
 */
final class EmployeeNotFoundException extends NotFoundException
{
    public static function withId(int $id): self
    {
        return self::withMessage("Employee with ID $id not found");
    }
}
