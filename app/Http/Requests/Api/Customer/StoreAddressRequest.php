<?php

namespace App\Http\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'company'       => ['nullable', 'string'],
            'address1'      => ['required', 'string'],
            'address2'      => ['nullable', 'string'],
            'city'          => ['required', 'string'],
            'province'      => ['nullable', 'string'],
            'province_code' => ['nullable', 'string'],
            'country'       => ['required', 'string'],
            'country_code'  => ['required', 'string', 'size:2'],
            'zip'           => ['required', 'string'],
            'phone'         => ['nullable', 'string'],
        ];
    }
}
