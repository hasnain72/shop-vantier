<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingZoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'countries'   => 'required|array|min:1',
            'countries.*' => 'string|size:2',
        ];
    }
}
