<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DataObjects\EmployeeData;
use App\Exceptions\Http\DuplicateEmailException;
use App\Exceptions\Http\EmployeeNotFoundException;
use App\Http\Controllers\EmployeeController;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Repositories\InMemoryEmployeeRepository;
use App\Repositories\InMemoryRepository;
use App\Services\EmployeeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\TestCase;

#[CoversClass(EmployeeController::class)]
#[UsesClass(EmployeeData::class)]
#[UsesClass(EmployeeResource::class)]
#[UsesClass(EmployeeService::class)]
#[UsesClass(InMemoryRepository::class)]
#[UsesClass(InMemoryEmployeeRepository::class)]
#[UsesClass(EmployeeNotFoundException::class)]
#[UsesClass(DuplicateEmailException::class)]
#[UsesClass(StoreEmployeeRequest::class)]
#[Group('integration')]
final class EmployeeApiTest extends TestCase
{
    #[TestDox('It returns all employees')]
    public function test_it_returns_all_employees(): void
    {
        $response = $this->getJson('/api/employees');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => 1,
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
            ]);
    }

    #[TestDox('It retrieves a single employee by ID')]
    public function test_it_retrieves_a_single_employee_by_id(): void
    {
        $response = $this->getJson('/api/employees/1');

        $response
            ->assertOk()
            ->assertExactJson([
                'id' => 1,
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
            ]);
    }

    #[TestDox('It returns 404 when employee does not exist')]
    public function test_it_returns_404_when_employee_does_not_exist(): void
    {
        $response = $this->getJson('/api/employees/999');

        $response->assertNotFound();
    }

    #[TestDox('It creates a new employee')]
    public function test_it_creates_a_new_employee(): void
    {
        $payload = [
            'firstName' => 'Grace',
            'lastName' => 'Hopper',
            'email' => 'grace.hopper@example.com',
        ];

        $createResponse = $this->postJson('/api/employees', $payload);

        $createResponse
            ->assertCreated()
            ->assertExactJson([
                'id' => 3,
                'firstName' => 'Grace',
                'lastName' => 'Hopper',
                'email' => 'grace.hopper@example.com',
            ]);
    }

    #[TestDox('It validates incoming employee data')]
    public function test_it_validates_incoming_employee_data(): void
    {
        $response = $this->postJson('/api/employees', [
            'firstName' => '',
            'email' => 'not-an-email',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['firstName', 'lastName', 'email']);
    }

    #[TestDox('It rejects duplicate employee emails')]
    public function test_it_rejects_duplicate_employee_emails(): void
    {
        $response = $this->postJson('/api/employees', [
            'firstName' => 'Johnny',
            'lastName' => 'Doe',
            'email' => 'JOHN.DOE@example.com',
        ]);

        $response
            ->assertConflict()
            ->assertJsonValidationErrors(['email']);
    }

    #[TestDox('It deletes an existing employee')]
    public function test_it_deletes_an_existing_employee(): void
    {
        $this->deleteJson('/api/employees/1')
            ->assertNoContent();
    }

    #[TestDox('It returns 404 when deleting non-existent employee')]
    public function test_it_returns_404_when_deleting_non_existent_employee(): void
    {
        $this->deleteJson('/api/employees/999')
            ->assertNotFound();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Unregister error listeners
        restore_error_handler();
        restore_exception_handler();
    }
}
