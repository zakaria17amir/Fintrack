<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash,credit_card,savings,investment'],
            'currency' => ['required', 'string', 'max:3'],
            'balance' => ['required', 'integer'],
            'color' => ['required', 'string', 'max:7'],
            'icon' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}
