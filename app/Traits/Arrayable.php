<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @template TKey of array-key
 * @template-covariant TValue
 */
trait Arrayable
{
    /**
     * @return array<TKey, TValue>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<TKey, TValue>
     */
    abstract public function toArray(): array;
}
