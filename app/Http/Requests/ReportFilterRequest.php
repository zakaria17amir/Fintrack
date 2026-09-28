<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Default to the last six whole months up to the end of this one. */
    protected function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            'date_from' => now()->subMonths(5)->startOfMonth()->toDateString(),
            'date_to' => now()->endOfMonth()->toDateString(),
            'categories' => [],
        ]);
    }

    public function rules(): array
    {
        return [
            'date_from' => ['required', 'date_format:Y-m-d'],
            'date_to' => ['required', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'categories' => ['array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
