<?php

namespace Tests\Unit\Repositories;

use App\DataObjects\EmployeeData;
use App\Repositories\InMemoryEmployeeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InMemoryEmployeeRepository::class)]
#[UsesClass(EmployeeData::class)]
class InMemoryEmployeeRepositoryTest extends TestCase
{
    private InMemoryEmployeeRepository $repository;

    #[TestDox('it returns an employee when the email exists')]
    public function testFindByEmail()
    {
        $employee = $this->repository->findByEmail('test@example.com');

        $this->assertNotNull($employee);
        $this->assertEquals('test@example.com', $employee->email);
        $this->assertEquals('Test', $employee->firstName);
        $this->assertEquals('User', $employee->lastName);
        $this->assertEquals(1, $employee->id);
    }

    #[TestDox('it returns null when the email does not exist')]
    public function testFindByEmail_NonExistent()
    {
        $employee = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($employee);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new InMemoryEmployeeRepository([
            EmployeeData::fromArray([
                'id' => 1,
                'email' => 'test@example.com',
                'firstName' => 'Test',
                'lastName' => 'User',
            ])
        ]);
    }
}
