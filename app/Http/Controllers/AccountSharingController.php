<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareAccountRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;

class AccountSharingController extends Controller
{
    public function index(Account $account)
    {
        $this->authorize('manageSharing', $account);

        $sharedUsers = $account->sharedUsers()->get();
        $availableUsers = User::where('id', '!=', $account->user_id)
            ->whereNotIn('id', $sharedUsers->pluck('id'))
            ->where('is_active', true)
            ->get();

        return view('accounts.sharing', compact('account', 'sharedUsers', 'availableUsers'));
    }

    public function store(ShareAccountRequest $request, Account $account)
    {
        $userIds = $request->validated()['user_ids'];
        $permission = $request->validated()['permission'];

        foreach ($userIds as $userId) {
            $account->sharedUsers()->attach($userId, ['permission' => $permission]);
        }

        return redirect()->route('accounts.sharing.index', $account)
            ->with('success', 'Account shared successfully.');
    }

    public function update(Request $request, Account $account, User $user)
    {
        $this->authorize('manageSharing', $account);

        $request->validate([
            'permission' => ['required', 'in:view,edit'],
        ]);

        $account->sharedUsers()->updateExistingPivot($user->id, [
            'permission' => $request->permission,
        ]);

        return redirect()->route('accounts.sharing.index', $account)
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Account $account, User $user)
    {
        $this->authorize('manageSharing', $account);

        $account->sharedUsers()->detach($user->id);

        return redirect()->route('accounts.sharing.index', $account)
            ->with('success', 'Access removed successfully.');
    }
}
