<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $categories = Category::orderBy('name')->get();

        $dateFrom = $request->get('date_from', now()->subMonths(6)->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfMonth()->toDateString());
        $selectedCategories = $request->get('categories', []);

        $query = $user->transactions()
            ->where('status', 'cleared')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Category breakdown
        $categoryBreakdown = (clone $query)
            ->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                $item->category = Category::find($item->category_id);
                return $item;
            });

        // Monthly trend
        $startDate = \Carbon\Carbon::parse($dateFrom)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($dateTo)->endOfMonth();
        $monthlyTrend = collect();

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $income = (clone $query)
                ->where('type', 'income')
                ->whereMonth('transaction_date', $current->month)
                ->whereYear('transaction_date', $current->year)
                ->sum('amount');

            $expense = (clone $query)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $current->month)
                ->whereYear('transaction_date', $current->year)
                ->sum('amount');

            $monthlyTrend->push([
                'month' => $current->format('M Y'),
                'income' => $income,
                'expense' => $expense,
            ]);

            $current->addMonth();
        }

        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');

        return view('reports.index', compact(
            'categories', 'categoryBreakdown', 'monthlyTrend',
            'dateFrom', 'dateTo', 'selectedCategories',
            'totalIncome', 'totalExpense'
        ));
    }
}
