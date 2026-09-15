<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $ownAccounts = $user->accounts()->latest()->get();
        $sharedAccounts = $user->sharedAccounts()->with('owner')->latest()->get();

        return view('accounts.index', compact('ownAccounts', 'sharedAccounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request)
    {
        $request->user()->accounts()->create($request->validated());

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);

        $account->load('owner');
        $transactions = $account->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->paginate(10);

        $permission = $account->getPermissionFor(auth()->user());

        return view('accounts.show', compact('account', 'transactions', 'permission'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);

        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $account->update($request->validated());

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
