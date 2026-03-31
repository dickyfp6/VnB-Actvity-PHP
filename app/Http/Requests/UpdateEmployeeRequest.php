<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && in_array(auth()->user()->role, ['hr', 'intercom']);
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'email' => [
                'email',
                'max:255',
                Rule::unique('employees', 'email')
                    ->ignore($this->employee->id)
                    ->whereNull('deleted_at'),
            ],
            'division_id' => 'nullable|integer|exists:master_divisions,id',
            'department_id' => 'nullable|integer|exists:master_departments,id',
            'position_id' => 'nullable|integer|exists:master_positions,id',
            'placement' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'employee_status' => ['string', 'max:50', Rule::exists('master_employee_statuses', 'name')],
            'induction_date' => 'nullable|date',
            'whatsapp' => 'nullable|string|max:20|regex:/^(\+62|0)[0-9]{9,12}$/',
            'manager_functional_id' => 'nullable|integer|exists:managers,id',
            'manager_operational_id' => 'nullable|integer|exists:managers,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email already registered',
            'whatsapp.regex' => 'Please enter valid Indonesian phone number',
        ];
    }
}
