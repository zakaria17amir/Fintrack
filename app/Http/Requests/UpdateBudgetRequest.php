<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('budget'));
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'month' => ['required', 'date_format:Y-m'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $budget = $this->route('budget');
            $exists = \App\Models\Budget::where('user_id', $this->user()->id)
                ->where('category_id', $this->category_id)
                ->whereDate('month', $this->month . '-01')
                ->where('id', '!=', $budget->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('category_id', 'You already have a budget for this category and month.');
            }
        });
    }
}
