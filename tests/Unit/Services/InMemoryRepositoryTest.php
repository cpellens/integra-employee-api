<?php

namespace Tests\Unit\Services;

use App\DataObjects\Contracts\DataObjectInterface;
use App\DataObjects\EmployeeData;
use App\Repositories\InMemoryRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InMemoryRepository::class)]
#[UsesClass(EmployeeData::class)]
#[Group('unit')]
class InMemoryRepositoryTest extends TestCase
{
    public static function provideObjects(): iterable
    {
        static $counts = [0, 1, 10, 250];

        foreach ($counts as $count) {
            $objects = [];
            for ($id = 1; $id <= $count; $id++) {
                $objects[] = self::createConfiguredStub(DataObjectInterface::class, [
                    'getId' => $id,
                ]);
            }

            yield "List of $count Objects" => [
                'objects' => $objects,
                'count' => $count,
            ];
        }
    }

    #[TestDox('should return null when record does not exist')]
    #[DataProvider('provideObjects')]
    public function testFind_NotFound(array $objects, int $count)
    {
        $repository = new InMemoryRepository(DataObjectInterface::class, $objects);
        self::assertNull($repository->find($count + 1), 'the record should be null since it does not exist');
    }

    #[TestDox('should return record when record exists')]
    #[DataProvider('provideObjects')]
    public function testFind_Found(array $objects, int $count)
    {
        if ($count === 0) {
            $this->markTestSkipped('No employees provided');
        }

        $repository = new InMemoryRepository(DataObjectInterface::class, $objects);
        $foundObject = $repository->find(1);

        self::assertInstanceOf(DataObjectInterface::class, $foundObject, 'the record should not be null since it exists');
        self::assertSame(1, $foundObject->getId(), 'the record should have the correct key');
    }

    #[TestDox('should remove the matching record from the repository')]
    #[DataProvider('provideObjects')]
    public function testDelete_ByKey(array $objects, int $count)
    {
        if ($count === 0) {
            $this->markTestSkipped('No record set provided');
        }

        $repository = new InMemoryRepository(DataObjectInterface::class, $objects);
        $result = $repository->delete(1);
        self::assertTrue($result, 'the delete method should return true, indicating the record was successfully deleted');
        self::assertNull($repository->find(1), 'the record should be null since it was deleted');
    }

    #[TestDox('should store a new record in the repository')]
    #[DataProvider('provideObjects')]
    public function testStore(array $objects, int $count)
    {
        $repository = new InMemoryRepository(EmployeeData::class, $objects);

        $object = $this->createMock(EmployeeData::class);
        $object->expects($this->once())
            ->method('getId')
            ->willReturn(0);

        $object->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
            ]);

        $storeResult = $repository->store($object);
        self::assertSame($count + 1, $storeResult->getId());
    }

    #[TestDox('should return all records from the repository')]
    #[DataProvider('provideObjects')]
    public function testGetAll(array $objects, int $count)
    {
        $repository = new InMemoryRepository(DataObjectInterface::class, $objects);
        $allRecords = $repository->getAll();
        self::assertCount($count, $allRecords, 'the getAll method should return all records from the repository');
    }
}
