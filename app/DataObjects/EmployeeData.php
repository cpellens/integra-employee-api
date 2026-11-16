<?php

declare(strict_types=1);

namespace App\DataObjects;

use App\DataObjects\Contracts\DataObjectInterface;
use App\Exceptions\Core\MissingKeysException;
use App\Traits\Arrayable;

readonly class EmployeeData implements DataObjectInterface
{
    use Arrayable;

    private const REQUIRED_KEYS = [
        'firstName',
        'lastName',
        'email',
    ];

    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?int   $id = null
    )
    {
    }

    /**
     * @param array{
     *     id: ?int,
     *     firstName: ?string,
     *     lastName: ?string,
     *     email: ?string,
     * } $data
     */
    public static function fromArray(array $data): static
    {
        $missingKeys = array_diff(self::REQUIRED_KEYS, array_keys($data));
        if (!empty($missingKeys)) throw MissingKeysException::withMissingKeys($missingKeys);

        return new self(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            isset($data['id']) ? (int)$data['id'] : null,
        );
    }

    /**
     * @return array{
     *     id: ?int,
     *     firstName: ?string,
     *     lastName: ?string,
     *     email: ?string,
     * }
     */
    public function toArray(): array
    {
        // Coalescing all fields because some packages may use reflection to instantiate an object
        // without its constructor. In this case, the firstName, lastName, and email fields may be *unset*.
        return [
            'id' => $this->getId(),
            'firstName' => $this->firstName ?? null,
            'lastName' => $this->lastName ?? null,
            'email' => $this->email ?? null,
        ];
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }
}
