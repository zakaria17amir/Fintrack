<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Account> */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        $types = ['bank', 'cash', 'credit_card', 'savings', 'investment'];
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
        $icons = ['wallet', 'bank', 'credit-card', 'piggy-bank', 'chart-line'];

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Main Checking', 'Savings Account', 'Credit Card', 'Cash Wallet', 'Investment Portfolio', 'Emergency Fund']),
            'type' => fake()->randomElement($types),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'HUF']),
            'balance' => fake()->numberBetween(10000, 5000000),
            'color' => fake()->randomElement($colors),
            'icon' => fake()->randomElement($icons),
            'is_active' => true,
        ];
    }
}
