<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DataObjects\Contracts\DataObjectInterface;
use Illuminate\Support\Collection;

/**
 * @template TModel of DataObjectInterface
 */
interface RepositoryInterface
{
    /**
     * @return class-string<TModel> Returns the fully qualified class name of the model.
     */
    public function getModelClass(): string;

    /**
     * @return Collection<TModel> A collection of models.
     */
    public function getAll(): Collection;

    /**
     * @param positive-int $id The ID of the model to retrieve.
     * @return TModel|null The model with the specified ID, or null if not found.
     */
    public function find(int $id): ?DataObjectInterface;

    /**
     * @param TModel|positive-int $model The model to delete. May be an instance of DeletableInterface, a Model, or an integer ID.
     * @return bool Returns true if the model was successfully deleted, false otherwise.
     */
    public function delete(DataObjectInterface|int $model): bool;

    /**
     * @param TModel $model The model to persist to the repository.
     * @return TModel Returns the stored model.
     */
    public function store(DataObjectInterface $model): DataObjectInterface;
}
