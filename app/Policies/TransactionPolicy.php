<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        if ($user->isAdmin()) return true;
        if ($transaction->user_id === $user->id) return true;

        $account = $transaction->account;
        return $account->sharedUsers()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        if ($user->isAdmin()) return true;
        if ($transaction->user_id === $user->id) return true;

        $account = $transaction->account;
        return $account->sharedUsers()
            ->where('user_id', $user->id)
            ->wherePivot('permission', 'edit')
            ->exists();
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        if ($user->isAdmin()) return true;
        if ($transaction->user_id === $user->id) return true;

        $account = $transaction->account;
        return $account->sharedUsers()
            ->where('user_id', $user->id)
            ->wherePivot('permission', 'edit')
            ->exists();
    }
}
