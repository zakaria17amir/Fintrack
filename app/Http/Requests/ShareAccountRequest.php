<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShareAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageSharing', $this->route('account'));
    }

    public function rules(): array
    {
        $account = $this->route('account');

        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'permission' => ['required', 'in:view,edit'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $account = $this->route('account');
            $userIds = $this->user_ids ?? [];

            foreach ($userIds as $userId) {
                if ($userId == $account->user_id) {
                    $validator->errors()->add('user_ids', 'You cannot share an account with its owner.');
                    break;
                }
            }
        });
    }
}
