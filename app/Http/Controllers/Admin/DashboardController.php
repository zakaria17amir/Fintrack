<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalAccounts = Account::count();
        $totalTransactions = Transaction::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentTransactions = Transaction::with(['user', 'account', 'category'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'totalAccounts', 'totalTransactions',
            'recentUsers', 'recentTransactions'
        ));
    }
}
