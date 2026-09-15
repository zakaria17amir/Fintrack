<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'type' => ['required', 'in:income,expense,both'],
            'color' => ['required', 'string', 'max:7'],
            'icon' => ['required', 'string', 'max:50'],
            'is_system' => ['boolean'],
        ];
    }
}
