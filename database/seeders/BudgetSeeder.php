<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();

        $currentMonth = now()->startOfMonth()->toDateString();
        $lastMonth = now()->subMonth()->startOfMonth()->toDateString();

        $johnBudgets = [
            ['slug' => 'food-dining', 'amount' => 60000],
            ['slug' => 'transportation', 'amount' => 20000],
            ['slug' => 'entertainment', 'amount' => 15000],
            ['slug' => 'shopping', 'amount' => 40000],
            ['slug' => 'utilities', 'amount' => 15000],
            ['slug' => 'housing', 'amount' => 125000],
        ];

        foreach ($johnBudgets as $budget) {
            $categoryId = Category::where('slug', $budget['slug'])->first()->id;

            // Current month
            Budget::create([
                'user_id' => $john->id,
                'category_id' => $categoryId,
                'amount' => $budget['amount'],
                'month' => $currentMonth,
            ]);

            // Last month
            Budget::create([
                'user_id' => $john->id,
                'category_id' => $categoryId,
                'amount' => $budget['amount'],
                'month' => $lastMonth,
            ]);
        }

        // Jane's budgets
        $janeBudgets = [
            ['slug' => 'food-dining', 'amount' => 40000],
            ['slug' => 'transportation', 'amount' => 10000],
            ['slug' => 'entertainment', 'amount' => 10000],
        ];

        foreach ($janeBudgets as $budget) {
            Budget::create([
                'user_id' => $jane->id,
                'category_id' => Category::where('slug', $budget['slug'])->first()->id,
                'amount' => $budget['amount'],
                'month' => $currentMonth,
            ]);
        }
    }
}
