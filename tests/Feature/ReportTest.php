<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_count_only_the_users_own_cleared_transactions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $otherAccount = Account::factory()->create(['user_id' => $other->id]);
        $base = ['category_id' => $category->id, 'type' => 'expense', 'transaction_date' => now()->toDateString()];

        Transaction::factory()->create([...$base, 'user_id' => $user->id, 'account_id' => $account->id, 'amount' => 3000, 'status' => 'cleared']);
        Transaction::factory()->create([...$base, 'user_id' => $user->id, 'account_id' => $account->id, 'amount' => 5000, 'status' => 'pending']);
        Transaction::factory()->create([...$base, 'user_id' => $other->id, 'account_id' => $otherAccount->id, 'amount' => 7000, 'status' => 'cleared']);

        $this->actingAs($user)->get(route('reports'))
            ->assertOk()
            ->assertViewHas('initial', fn ($initial) => $initial['data']['totals']['expense'] === 3000)
            ->assertSee('id="reports-root"', false);
    }
}
