<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();

        // John's accounts
        $johnChecking = Account::create([
            'user_id' => $john->id,
            'name' => 'Main Checking',
            'type' => 'bank',
            'currency' => 'USD',
            'balance' => 2450000,
            'color' => '#3B82F6',
            'icon' => 'bank',
            'is_active' => true,
        ]);

        $johnSavings = Account::create([
            'user_id' => $john->id,
            'name' => 'Savings Account',
            'type' => 'savings',
            'currency' => 'USD',
            'balance' => 8500000,
            'color' => '#10B981',
            'icon' => 'piggy-bank',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $john->id,
            'name' => 'Credit Card',
            'type' => 'credit_card',
            'currency' => 'USD',
            'balance' => -125000,
            'color' => '#EF4444',
            'icon' => 'credit-card',
            'is_active' => true,
        ]);

        // Jane's accounts
        Account::create([
            'user_id' => $jane->id,
            'name' => 'Personal Account',
            'type' => 'bank',
            'currency' => 'EUR',
            'balance' => 3200000,
            'color' => '#8B5CF6',
            'icon' => 'bank',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $jane->id,
            'name' => 'Cash Wallet',
            'type' => 'cash',
            'currency' => 'EUR',
            'balance' => 45000,
            'color' => '#F59E0B',
            'icon' => 'wallet',
            'is_active' => true,
        ]);

        // Bob's accounts
        Account::create([
            'user_id' => $bob->id,
            'name' => 'Investment Portfolio',
            'type' => 'investment',
            'currency' => 'USD',
            'balance' => 15000000,
            'color' => '#06B6D4',
            'icon' => 'chart-line',
            'is_active' => true,
        ]);

        // Share John's checking account with Jane (edit) and Bob (view)
        $johnChecking->sharedUsers()->attach($jane->id, ['permission' => 'edit']);
        $johnChecking->sharedUsers()->attach($bob->id, ['permission' => 'view']);

        // Share John's savings with Jane (view)
        $johnSavings->sharedUsers()->attach($jane->id, ['permission' => 'view']);
    }
}
