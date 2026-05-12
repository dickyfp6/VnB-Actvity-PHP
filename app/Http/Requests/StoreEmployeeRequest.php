<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && in_array(auth()->user()->role, ['hr', 'intercom']);
    }

    public function rules(): array
    {
        return [
            'employee_number' => 'required|string|max:50|unique:employees',
            'name' => 'required|string|max:255',
            'date_joined' => 'required|date|before_or_equal:today',
            'company' => 'required|string|max:100',
            'division_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'position_id' => 'nullable|integer',
            'placement' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:employees',
            'whatsapp' => 'nullable|string|max:20|regex:/^(\+62|0)[0-9]{9,12}$/',
            'manager_functional_id' => 'nullable|integer|exists:managers,id',
            'manager_operational_id' => 'nullable|integer|exists:managers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_number.unique' => 'Employee number already exists',
            'email.unique' => 'Email already registered',
            'whatsapp.regex' => 'Please enter valid Indonesian phone number',
            'date_joined.before_or_equal' => 'Join date cannot be in the future',
        ];
    }
}
