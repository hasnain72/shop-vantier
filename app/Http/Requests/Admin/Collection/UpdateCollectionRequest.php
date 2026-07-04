<?php

namespace App\Http\Requests\Admin\Collection;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['required', 'in:manual,best-selling,alpha-asc,alpha-desc,price-asc,price-desc,created-asc,created-desc'],
            'template_suffix' => ['nullable', 'string', 'max:255'],
            'published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_position' => ['nullable', 'integer'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ];
    }
}

