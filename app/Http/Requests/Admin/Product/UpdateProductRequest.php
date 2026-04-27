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
            'tags' => ['nullable', 'string'],
            'status' => ['required', 'in:active,archived,draft'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'requires_shipping' => ['sometimes', 'boolean'],
            'taxable' => ['sometimes', 'boolean'],
            'collection_ids' => ['sometimes', 'array'],
            'collection_ids.*' => ['integer', 'exists:collections,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['tags']) && is_string($data['tags'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
            $data['tags'] = $tags ?: null;
        }

        return $data;
    }
}

