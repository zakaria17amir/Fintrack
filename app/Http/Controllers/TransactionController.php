<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $accountIds = $user->accounts()->pluck('id')
            ->merge($user->sharedAccounts()->pluck('accounts.id'));

        $query = Transaction::whereIn('account_id', $accountIds)
            ->with(['account', 'category']);

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();

        $accounts = Account::whereIn('id', $accountIds)->get();
        $categories = Category::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $accounts = $user->accounts()
            ->where('is_active', true)
            ->get()
            ->merge(
                $user->sharedAccounts()
                    ->wherePivot('permission', 'edit')
                    ->where('is_active', true)
                    ->get()
            );

        $categories = Category::orderBy('name')->get();

        return view('transactions.create', compact('accounts', 'categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        unset($data['receipt']);
        $data['is_recurring'] = $request->boolean('is_recurring');

        $transaction = Transaction::create($data);
        $this->applyBalanceChange($transaction, 1);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $user = auth()->user();
        $accounts = $user->accounts()
            ->where('is_active', true)
            ->get()
            ->merge(
                $user->sharedAccounts()
                    ->wherePivot('permission', 'edit')
                    ->where('is_active', true)
                    ->get()
            );

        $categories = Category::orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $oldTransaction = clone $transaction;

        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk('public')->delete($transaction->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        unset($data['receipt']);
        $data['is_recurring'] = $request->boolean('is_recurring');

        $transaction->update($data);

        $this->applyBalanceChange($oldTransaction, -1);
        $this->applyBalanceChange($transaction, 1);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        if ($transaction->receipt_path) {
            Storage::disk('public')->delete($transaction->receipt_path);
        }

        $this->applyBalanceChange($transaction, -1);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    private function applyBalanceChange(Transaction $transaction, int $multiplier = 1): void
    {
        $account = $transaction->account;

        if (! $account) {
            return;
        }

        if ($transaction->type === 'income') {
            $account->balance += $transaction->amount * $multiplier;
        } elseif ($transaction->type === 'expense') {
            $account->balance -= $transaction->amount * $multiplier;
        }

        $account->save();
    }
}
