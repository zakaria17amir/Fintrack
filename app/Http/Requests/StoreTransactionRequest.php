<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A transaction changes its account's balance, so the user must be allowed to edit that
        // account. A missing account is left to the `exists` rule (422).
        $account = Account::find($this->input('account_id'));

        return ! $account || $this->user()->can('update', $account);
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:income,expense'],
            'status' => ['required', 'in:pending,cleared,cancelled'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'is_recurring' => ['boolean'],
            'recurring_interval' => ['nullable', 'required_if:is_recurring,1', 'in:daily,weekly,monthly,yearly'],
        ];
    }
}
