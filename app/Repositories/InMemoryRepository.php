<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DataObjects\Contracts\DataObjectInterface;
use App\Exceptions\Core\InvalidClassException;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * @template TModel of DataObjectInterface
 * @implements RepositoryInterface<TModel>
 */
class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var Collection<array-key, TModel> $records The underlying collection of records
     */
    protected readonly Collection $records;

    /**
     * @var int $autoIncrement The next available primary key value
     */
    private int $autoIncrement = 0;

    /**
     * @param class-string<TModel> $modelClass The FQN of the model class
     * @param iterable<TModel> $records The underlying collection of records
     */
    public function __construct(private readonly string $modelClass, iterable $records = [])
    {
        if (!is_a($modelClass, DataObjectInterface::class, true)) {
            throw InvalidClassException::withExpected(DataObjectInterface::class, $modelClass);
        }

        // Ensure the model isn't abstract. Cannot instantiate abstract class
        $this->records = Collection::make($records)->keyBy(static fn(DataObjectInterface $model) => $model->getId());

        // NOTE: Assuming that the primary key is an integer
        // This would look different if the primary key was a string, such as for a UUID
        // Defaults to 0 if the returned value is falsey
        $this->autoIncrement = $this->records->max(static fn(DataObjectInterface $model) => $model->getId()) ?: 0;
    }

    public function getAll(): Collection
    {
        return $this->records->values();
    }

    public function find(int $id): ?DataObjectInterface
    {
        return $this->records->firstWhere(static fn(DataObjectInterface $model) => $model->getId() === $id);
    }

    public function delete(DataObjectInterface|int $model): bool
    {
        $modelId = is_int($model) ? $model : $model->getId();
        if (!$this->records->has($modelId))
            return false;

        $this->records->forget($modelId);
        return true;
    }

    public function store(DataObjectInterface $model): DataObjectInterface
    {
        $modelId = $model->getId();
        if ($modelId === 0) {
            $modelId = $this->nextId();
            $model = $this->getModelClass()::fromArray([
                ...$model->toArray(),
                'id' => $modelId,
            ]);
        }

        $this->records->put($modelId, $model);
        return $model;
    }

    /**
     * @return positive-int Increments the internal counter and returns the result.
     */
    private function nextId(): int
    {
        return ++$this->autoIncrement;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }
}
