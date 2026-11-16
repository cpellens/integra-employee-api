<?php

namespace Tests\Unit\Services;

use App\DataObjects\EmployeeData;
use App\Exceptions\Http\DuplicateEmailException;
use App\Exceptions\Http\EmployeeNotFoundException;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\EmployeeService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmployeeService::class)]
#[UsesClass(EmployeeData::class)]
#[UsesClass(EmployeeNotFoundException::class)]
#[UsesClass(DuplicateEmailException::class)]
class EmployeeServiceTest extends TestCase
{
    #[TestDox('it deletes an existing employee successfully')]
    public function testDeleteEmployee()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $service = new EmployeeService($repository);
        $service->deleteEmployee(1);
    }

    #[TestDox('it throws an exception when trying to delete a non-existing employee')]
    public function testDeleteEmployee_NotExists()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(false);

        $this->expectException(EmployeeNotFoundException::class);

        $service = new EmployeeService($repository);
        $service->deleteEmployee(1);
    }

    #[TestDox('it creates a new employee successfully')]
    public function testCreateEmployee()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);
        $repository
            ->expects($this->once())
            ->method('store')
            ->willReturnArgument(0);

        $employee = $this->getMockBuilder(EmployeeData::class)
            ->setConstructorArgs([
                'email' => 'test@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ])
            ->getMock();

        $service = new EmployeeService($repository);
        $service->createEmployee($employee);
    }

    #[TestDox('it fails to create a new employee with an existing email')]
    public function testCreateEmployee_DuplicateEmail()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(EmployeeData::fromArray([
                'id' => 1,
                'email' => 'test@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ]));
        $repository
            ->expects($this->never())
            ->method('store');

        $employee = $this->getMockBuilder(EmployeeData::class)
            ->setConstructorArgs([
                'email' => 'test@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ])
            ->getMock();

        $service = new EmployeeService($repository);

        $this->expectException(DuplicateEmailException::class);
        $service->createEmployee($employee);
    }

    #[TestDox('it successfully fetches the employee from the repository')]
    public function testGetEmployeeById()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(EmployeeData::fromArray([
                'id' => 1,
                'email' => 'test@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ]));

        $service = new EmployeeService($repository);
        $employee = $service->getEmployeeById(1);

        self::assertSame(1, $employee->id);
    }

    #[TestDox('it successfully fetches all employees from the repository')]
    public function testGetAllEmployees()
    {
        $repository = $this->createMock(EmployeeRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('getAll')
            ->willReturn(Collection::make());

        $service = new EmployeeService($repository);
        $employees = $service->getAllEmployees();

        self::assertEmpty($employees);
    }
}
