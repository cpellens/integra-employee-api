<?php

namespace Tests\Unit\DataObjects;

use App\DataObjects\EmployeeData;
use App\Exceptions\Core\MissingKeysException;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmployeeData::class)]
#[Group('unit')]
class EmployeeDataTest extends TestCase
{
    /**
     * This is a data provider for testing {@see EmployeeData}.
     * It provides valid and invalid employee data arrays for testing various scenarios
     * on the {@see EmployeeData::fromArray()} method.
     *
     * @return array[]
     */
    public static function provideEmployeeDataArrays(): array
    {
        $faker = Factory::create();

        return [
            'Valid - Without ID' => [
                'expectedValid' => true,
                'data' => [
                    'firstName' => $faker->firstName,
                    'lastName' => $faker->lastName,
                    'email' => $faker->email
                ]
            ],
            'Invalid - Empty array' => [
                'expectedValid' => false,
                'data' => []
            ],
            'Invalid - Missing email' => [
                'expectedValid' => false,
                'data' => [
                    'firstName' => $faker->firstName,
                    'lastName' => $faker->lastName
                ]
            ],
            'Invalid - Missing last name' => [
                'expectedValid' => false,
                'data' => [
                    'firstName' => $faker->firstName,
                    'email' => $faker->email
                ]
            ],
            'Invalid - Missing first name' => [
                'expectedValid' => false,
                'data' => [
                    'lastName' => $faker->lastName,
                    'email' => $faker->email
                ]
            ],
            'Valid - With ID' => [
                'expectedValid' => true,
                'data' => [
                    'id' => 1,
                    'firstName' => $faker->firstName,
                    'lastName' => $faker->lastName,
                    'email' => $faker->email
                ]
            ]
        ];
    }

    #[TestDox('should create EmployeeData object from array')]
    #[DataProvider('provideEmployeeDataArrays')]
    public function testFromArray(bool $expectedValid, array $data)
    {
        if (!$expectedValid)
            $this->expectException(MissingKeysException::class);

        $employeeData = EmployeeData::fromArray($data);
        self::assertSame($data['id'], $employeeData->id, 'ID should match');
        self::assertSame($data['id'] ?? 0, $employeeData->getId(), 'ID getter should match');
        self::assertSame($data['firstName'], $employeeData->firstName, 'First name should match');
        self::assertSame($data['lastName'], $employeeData->lastName, 'Last name should match');
        self::assertSame($data['email'], $employeeData->email, 'Email should match');
    }

    #[TestDox('should return the correct ID of the EmployeeData object')]
    public function testGetId()
    {
        $employeeInputArray = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com'
        ];

        $employeeDataWithoutID = EmployeeData::fromArray($employeeInputArray);
        $employeeDataWithID = EmployeeData::fromArray($employeeInputArray + ['id' => 132]);

        self::assertSame(132, $employeeDataWithID->getId(), 'ID should match');
        self::assertSame(0, $employeeDataWithoutID->getId(), 'ID getter should default to zero');
        self::assertNull($employeeDataWithoutID->id, 'ID property should be null');
    }

    #[TestDox('should return the correct array representation')]
    public function testToArray()
    {
        $inputArray = [
            'id' => 0,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com'
        ];

        $employeeData = EmployeeData::fromArray($inputArray);
        self::assertSame($inputArray, $employeeData->toArray(), 'Array representation should match');
    }
}
