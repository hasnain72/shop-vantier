<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'rate_type'          => 'required|in:flat,free,price_based,weight_based',
            'price'              => 'required|numeric|min:0',
            'min_order_subtotal' => 'nullable|numeric|min:0',
            'max_order_subtotal' => 'nullable|numeric|min:0|gte:min_order_subtotal',
            'min_weight'         => 'nullable|numeric|min:0',
            'max_weight'         => 'nullable|numeric|min:0|gte:min_weight',
            'is_active'          => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'max_order_subtotal.gte' => 'Max order amount must be greater than or equal to min.',
            'max_weight.gte'         => 'Max weight must be greater than or equal to min.',
        ];
    }
}
