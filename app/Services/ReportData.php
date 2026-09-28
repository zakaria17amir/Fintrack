<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

/**
 * Report figures for a user's own cleared transactions, in integer cents.
 * Three grouped queries regardless of the date range.
 */
class ReportData
{
    /**
     * @param  list<int>  $categoryIds  Empty means every category.
     * @return array{
     *     totals: array{income: int, expense: int, net: int},
     *     byCategory: list<array{id: int, name: string, color: string, total: int}>,
     *     monthly: list<array{month: string, income: int, expense: int}>
     * }
     */
    public function for(User $user, string $from, string $to, array $categoryIds = []): array
    {
        // Columns are qualified because the breakdown joins categories, which also has a `type`.
        $scope = fn () => $user->transactions()
            ->where('transactions.status', 'cleared')
            ->whereBetween('transactions.transaction_date', [$from, $to])
            ->when($categoryIds, fn ($q) => $q->whereIn('transactions.category_id', $categoryIds));

        $totals = $scope()
            ->selectRaw('transactions.type, SUM(transactions.amount) as total')
            ->groupBy('transactions.type')
            ->pluck('total', 'type');
        $income = (int) ($totals['income'] ?? 0);
        $expense = (int) ($totals['expense'] ?? 0);

        $byCategory = $scope()
            ->where('transactions.type', 'expense')
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->selectRaw('categories.id, categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name, 'color' => $row->color, 'total' => (int) $row->total])
            ->all();

        // ponytail: strftime is SQLite-only (FinTrack's only driver); use DATE_FORMAT/to_char if that changes.
        $perMonth = $scope()
            ->selectRaw("strftime('%Y-%m', transactions.transaction_date) as month, transactions.type, SUM(transactions.amount) as total")
            ->groupBy('month', 'transactions.type')
            ->get()
            ->groupBy('month');

        $monthly = [];
        foreach (CarbonPeriod::create(Carbon::parse($from)->startOfMonth(), '1 month', Carbon::parse($to)->startOfMonth()) as $month) {
            $key = $month->format('Y-m');
            $rows = ($perMonth[$key] ?? collect())->pluck('total', 'type');
            $monthly[] = ['month' => $key, 'income' => (int) ($rows['income'] ?? 0), 'expense' => (int) ($rows['expense'] ?? 0)];
        }

        return [
            'totals' => ['income' => $income, 'expense' => $expense, 'net' => $income - $expense],
            'byCategory' => $byCategory,
            'monthly' => $monthly,
        ];
    }
}
