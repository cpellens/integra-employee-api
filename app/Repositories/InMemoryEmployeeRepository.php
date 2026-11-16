<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DataObjects\EmployeeData;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

/**
 * @extends InMemoryRepository<EmployeeData>
 */
final class InMemoryEmployeeRepository extends InMemoryRepository implements EmployeeRepositoryInterface
{
    public function __construct(iterable $records = [])
    {
        parent::__construct(EmployeeData::class, $records);
    }

    public function findByEmail(string $email): ?EmployeeData
    {
        return $this->records->first(static fn(EmployeeData $record) => strcasecmp($record->email, $email) === 0);
    }
}
