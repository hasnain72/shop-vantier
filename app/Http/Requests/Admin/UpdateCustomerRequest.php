<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', Rule::unique('customers', 'email')->ignore($this->customer)],
            'phone'             => ['nullable', 'string', 'max:50'],
            'state'             => ['required', 'in:enabled,disabled,invited,declined'],
            'tax_exempt'        => ['sometimes', 'boolean'],
            'note'              => ['nullable', 'string'],
            'tags'              => ['nullable', 'string'],
            'accepts_marketing' => ['sometimes', 'boolean'],
        ];
    }
}
