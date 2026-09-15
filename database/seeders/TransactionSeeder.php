<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();

        $incomeCategories = Category::whereIn('type', ['income', 'both'])->pluck('id')->toArray();
        $expenseCategories = Category::whereIn('type', ['expense', 'both'])->pluck('id')->toArray();

        $johnAccounts = $john->accounts->pluck('id')->toArray();
        $janeAccounts = $jane->accounts->pluck('id')->toArray();

        // Generate transactions for the last 6 months
        for ($month = 5; $month >= 0; $month--) {
            $date = now()->subMonths($month);

            // John's monthly salary
            Transaction::create([
                'user_id' => $john->id,
                'account_id' => $johnAccounts[0],
                'category_id' => Category::where('slug', 'salary')->first()->id,
                'title' => 'Monthly Salary',
                'amount' => 550000,
                'type' => 'income',
                'status' => 'cleared',
                'transaction_date' => $date->copy()->startOfMonth()->addDays(0),
                'is_recurring' => true,
                'recurring_interval' => 'monthly',
            ]);

            // John's expenses
            $expenses = [
                ['title' => 'Grocery Shopping', 'slug' => 'food-dining', 'min' => 8000, 'max' => 25000],
                ['title' => 'Electric Bill', 'slug' => 'utilities', 'min' => 5000, 'max' => 12000],
                ['title' => 'Gas Station', 'slug' => 'transportation', 'min' => 4000, 'max' => 8000],
                ['title' => 'Monthly Rent', 'slug' => 'housing', 'min' => 120000, 'max' => 120000],
                ['title' => 'Netflix & Spotify', 'slug' => 'entertainment', 'min' => 2500, 'max' => 2500],
                ['title' => 'Restaurant Dinner', 'slug' => 'food-dining', 'min' => 3000, 'max' => 8000],
                ['title' => 'Online Shopping', 'slug' => 'shopping', 'min' => 5000, 'max' => 30000],
            ];

            foreach ($expenses as $expense) {
                Transaction::create([
                    'user_id' => $john->id,
                    'account_id' => fake()->randomElement($johnAccounts),
                    'category_id' => Category::where('slug', $expense['slug'])->first()->id,
                    'title' => $expense['title'],
                    'amount' => fake()->numberBetween($expense['min'], $expense['max']),
                    'type' => 'expense',
                    'status' => 'cleared',
                    'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 27)),
                    'is_recurring' => in_array($expense['title'], ['Monthly Rent', 'Netflix & Spotify']),
                    'recurring_interval' => in_array($expense['title'], ['Monthly Rent', 'Netflix & Spotify']) ? 'monthly' : null,
                ]);
            }

            // Jane's income
            Transaction::create([
                'user_id' => $jane->id,
                'account_id' => $janeAccounts[0],
                'category_id' => Category::where('slug', 'freelance')->first()->id,
                'title' => 'Freelance Payment',
                'amount' => 380000,
                'type' => 'income',
                'status' => 'cleared',
                'transaction_date' => $date->copy()->startOfMonth()->addDays(4),
                'is_recurring' => false,
            ]);

            // Jane's expenses
            for ($i = 0; $i < 4; $i++) {
                Transaction::create([
                    'user_id' => $jane->id,
                    'account_id' => fake()->randomElement($janeAccounts),
                    'category_id' => fake()->randomElement($expenseCategories),
                    'title' => fake()->randomElement(['Coffee Shop', 'Bus Pass', 'Groceries', 'Phone Bill', 'Gym Membership', 'Books']),
                    'amount' => fake()->numberBetween(1000, 15000),
                    'type' => 'expense',
                    'status' => fake()->randomElement(['cleared', 'cleared', 'cleared', 'pending']),
                    'transaction_date' => $date->copy()->startOfMonth()->addDays(rand(1, 27)),
                    'is_recurring' => false,
                ]);
            }
        }

        // Add a couple pending/cancelled for variety
        Transaction::create([
            'user_id' => $john->id,
            'account_id' => $johnAccounts[0],
            'category_id' => Category::where('slug', 'travel')->first()->id,
            'title' => 'Flight Booking (Refunded)',
            'amount' => 45000,
            'type' => 'expense',
            'status' => 'cancelled',
            'transaction_date' => now()->subDays(3),
            'notes' => 'Trip was cancelled, refund processed',
            'is_recurring' => false,
        ]);
    }
}
