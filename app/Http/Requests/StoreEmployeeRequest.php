<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DataObjects\EmployeeData;
use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'min:1', 'max:100'],
            'lastName' => ['required', 'string', 'min:1', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'firstName.required' => 'The firstName field is required.',
            'lastName.required' => 'The lastName field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }

    public function toDataObject(): EmployeeData
    {
        /** @var array{firstName: string, lastName: string, email: string} $validated */
        $validated = $this->validated();

        return EmployeeData::fromArray($validated);
    }
}
