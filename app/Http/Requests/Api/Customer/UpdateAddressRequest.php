<?php

namespace App\Http\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'company'       => ['nullable', 'string'],
            'address1'      => ['sometimes', 'string'],
            'address2'      => ['nullable', 'string'],
            'city'          => ['sometimes', 'string'],
            'province'      => ['nullable', 'string'],
            'province_code' => ['nullable', 'string'],
            'country'       => ['sometimes', 'string'],
            'country_code'  => ['sometimes', 'string', 'size:2'],
            'zip'           => ['sometimes', 'string'],
            'phone'         => ['nullable', 'string'],
        ];
    }
}
