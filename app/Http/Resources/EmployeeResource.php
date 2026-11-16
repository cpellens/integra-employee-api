<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataObjects\EmployeeData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EmployeeData
 */
final class EmployeeResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array{
     *     id: ?int,
     *     firstName: ?string,
     *     lastName: ?string,
     *     email: ?string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
