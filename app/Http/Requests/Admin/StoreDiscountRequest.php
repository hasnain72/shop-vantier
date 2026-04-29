<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'                    => 'required|string|max:255',
            'value_type'               => 'required|in:fixed_amount,percentage,free_shipping,buy_x_get_y',
            'value'                    => 'required_unless:value_type,free_shipping|nullable|numeric|min:0',
            'codes'                    => 'required|array|min:1',
            'codes.*'                  => 'required|string|max:255',
            'starts_at'                => 'required|date',
            'ends_at'                  => 'nullable|date|after:starts_at',
            'usage_limit'              => 'nullable|integer|min:1',
            'once_per_customer'        => 'boolean',
            'customer_selection'       => 'in:all,prerequisite',
            'target_selection'         => 'in:all,entitled',
            'min_purchase_amount'      => 'nullable|numeric|min:0',
            'min_quantity'             => 'nullable|integer|min:1',
            'entitled_collection_ids'  => 'nullable|array',
            'entitled_collection_ids.*'=> 'integer',
            'entitled_product_ids'     => 'nullable|array',
            'entitled_product_ids.*'   => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'value.required_unless' => 'Discount value is required.',
            'codes.required'        => 'At least one discount code is required.',
        ];
    }
}
