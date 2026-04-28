<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:customers,email'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'password'          => ['nullable', 'string', 'min:8'],
            'state'             => ['required', 'in:enabled,disabled,invited,declined'],
            'tax_exempt'        => ['sometimes', 'boolean'],
            'note'              => ['nullable', 'string'],
            'tags'              => ['nullable', 'string'],
            'accepts_marketing' => ['sometimes', 'boolean'],
            // default address
            'address.first_name'   => ['nullable', 'string'],
            'address.last_name'    => ['nullable', 'string'],
            'address.address1'     => ['nullable', 'string'],
            'address.city'         => ['nullable', 'string'],
            'address.country'      => ['nullable', 'string'],
            'address.country_code' => ['nullable', 'string', 'size:2'],
            'address.zip'          => ['nullable', 'string'],
            'address.phone'        => ['nullable', 'string'],
        ];
    }
}
