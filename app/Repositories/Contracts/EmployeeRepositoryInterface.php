<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DataObjects\EmployeeData;

/**
 * @extends RepositoryInterface<EmployeeData>
 */
interface EmployeeRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?EmployeeData;
}
