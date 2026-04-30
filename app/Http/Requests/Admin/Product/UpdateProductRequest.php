<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body_html' => ['nullable', 'string'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'product_type_id' => ['nullable', 'integer', 'exists:product_types,id'],
            'tags'   => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'status' => ['required', 'in:active,archived,draft'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'requires_shipping' => ['sometimes', 'boolean'],
            'taxable' => ['sometimes', 'boolean'],
            'collection_ids' => ['sometimes', 'array'],
            'collection_ids.*' => ['integer', 'exists:collections,id'],
            'images'          => ['nullable', 'array'],
            'images.*'        => ['image', 'max:5120'],
            'delete_image_ids'   => ['nullable', 'array'],
            'delete_image_ids.*' => ['integer'],
        ];
    }
}

