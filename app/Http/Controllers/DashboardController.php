<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $now = now();

        $totalBalance = $user->accounts()->where('is_active', true)->sum('balance');

        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $budgets = $user->budgets()
            ->with('category')
            ->whereDate('month', $now->copy()->startOfMonth()->toDateString())
            ->get();

        $categoryBreakdown = $user->transactions()
            ->where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                $item->category = \App\Models\Category::find($item->category_id);
                return $item;
            });

        $monthlyTrend = collect(range(5, 0))->map(function ($i) use ($user) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'income' => $user->transactions()
                    ->where('type', 'income')->where('status', 'cleared')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('amount'),
                'expense' => $user->transactions()
                    ->where('type', 'expense')->where('status', 'cleared')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('amount'),
            ];
        });

        return view('dashboard', compact(
            'totalBalance', 'monthlyIncome', 'monthlyExpense',
            'recentTransactions', 'budgets', 'categoryBreakdown', 'monthlyTrend'
        ));
    }
}
