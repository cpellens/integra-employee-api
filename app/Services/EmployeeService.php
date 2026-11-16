<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\EmployeeData;
use App\Exceptions\Http\DuplicateEmailException;
use App\Exceptions\Http\EmployeeNotFoundException;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * This service is responsible for managing employee-related operations.
 * It provides methods for retrieving, creating, and deleting employee data.
 * Additionally, it ensures that an email is unique before creating a new employee.
 */
final readonly class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepository)
    {
    }

    /**
     * @return Collection<EmployeeData>
     */
    public function getAllEmployees(): Collection
    {
        return $this->employeeRepository->getAll();
    }

    public function getEmployeeById(int $id): EmployeeData
    {
        return $this->employeeRepository->find($id)
            ?? throw EmployeeNotFoundException::withId($id);
    }

    public function createEmployee(EmployeeData $employeeData): EmployeeData
    {
        if ($this->employeeRepository->findByEmail($employeeData->email)) {
            throw DuplicateEmailException::withEmail($employeeData->email);
        }

        return $this->employeeRepository->store($employeeData);
    }

    public function deleteEmployee(int $id): void
    {
        if (!$this->employeeRepository->delete($id)) {
            throw EmployeeNotFoundException::withId($id);
        }
    }
}
