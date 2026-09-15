<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Transaction> */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense', 'expense', 'expense']);
        $isRecurring = fake()->boolean(20);

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->numberBetween(500, 500000),
            'type' => $type,
            'status' => fake()->randomElement(['pending', 'cleared', 'cleared', 'cleared', 'cancelled']),
            'transaction_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'notes' => fake()->optional(0.3)->sentence(),
            'receipt_path' => null,
            'is_recurring' => $isRecurring,
            'recurring_interval' => $isRecurring ? fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']) : null,
        ];
    }
}
