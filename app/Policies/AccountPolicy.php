<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Account $account): bool
    {
        if ($user->isAdmin()) return true;
        if ($account->isOwnedBy($user)) return true;

        return $account->sharedUsers()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Account $account): bool
    {
        if ($user->isAdmin()) return true;
        if ($account->isOwnedBy($user)) return true;

        return $account->sharedUsers()
            ->where('user_id', $user->id)
            ->wherePivot('permission', 'edit')
            ->exists();
    }

    public function delete(User $user, Account $account): bool
    {
        if ($user->isAdmin()) return true;

        return $account->isOwnedBy($user);
    }

    public function manageSharing(User $user, Account $account): bool
    {
        if ($user->isAdmin()) return true;

        return $account->isOwnedBy($user);
    }
}
