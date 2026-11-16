<?php

namespace App\DataObjects\Contracts;

use JsonSerializable;

interface DataObjectInterface extends JsonSerializable
{
    /**
     * A getter for the unique ID of the data object which provides
     * a common way to identify the primary key of the object, if it differs from
     * "id".
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Returns the data object as an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Creates a new data object from an array of data.
     *
     * @param array $data The data to create the object from.
     * @return static The created data object.
     */
    public static function fromArray(array $data): static;
}
