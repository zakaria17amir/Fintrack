<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionBalanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Account $account;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 10000]);
        $this->category = Category::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'title' => 'Groceries',
            'amount' => 2500,
            'type' => 'expense',
            'status' => 'cleared',
            'transaction_date' => '2026-09-01',
        ], $overrides);
    }

    private function storeTransaction(array $overrides = []): Transaction
    {
        $this->actingAs($this->user)->post(route('transactions.store'), $this->payload($overrides))
            ->assertRedirect(route('transactions.index'));

        return Transaction::latest('id')->firstOrFail();
    }

    private function balance(Account $account): int
    {
        return $account->fresh()->balance;
    }

    public function test_storing_an_expense_lowers_the_balance(): void
    {
        $this->storeTransaction();

        $this->assertSame(7500, $this->balance($this->account));
    }

    public function test_storing_income_raises_the_balance(): void
    {
        $this->storeTransaction(['type' => 'income', 'amount' => 1000]);

        $this->assertSame(11000, $this->balance($this->account));
    }

    public function test_changing_the_amount_replaces_the_old_effect(): void
    {
        $transaction = $this->storeTransaction();

        $this->actingAs($this->user)->put(route('transactions.update', $transaction), $this->payload(['amount' => 4000]))
            ->assertRedirect(route('transactions.index'));

        $this->assertSame(6000, $this->balance($this->account));
    }

    public function test_changing_expense_to_income_reverses_the_effect(): void
    {
        $transaction = $this->storeTransaction();

        $this->actingAs($this->user)->put(route('transactions.update', $transaction), $this->payload(['type' => 'income']));

        $this->assertSame(12500, $this->balance($this->account));
    }

    public function test_moving_a_transaction_to_another_account_reverts_the_old_and_applies_the_new_once(): void
    {
        $other = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 10000]);
        $transaction = $this->storeTransaction();

        $this->actingAs($this->user)->put(route('transactions.update', $transaction), $this->payload(['account_id' => $other->id]));

        $this->assertSame(10000, $this->balance($this->account));
        $this->assertSame(7500, $this->balance($other));
    }

    public function test_deleting_a_transaction_restores_the_balance(): void
    {
        $transaction = $this->storeTransaction();

        $this->actingAs($this->user)->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'));

        $this->assertSame(10000, $this->balance($this->account));
    }

    public function test_a_failed_balance_update_does_not_leave_the_transaction_behind(): void
    {
        Account::saving(fn () => throw new \RuntimeException('disk full'));

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->user)->post(route('transactions.store'), $this->payload());
            $this->fail('Expected the balance update to throw.');
        } catch (\RuntimeException) {
        }

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_cannot_move_a_transaction_onto_someone_elses_account(): void
    {
        $foreign = Account::factory()->create(['balance' => 10000]);
        $transaction = $this->storeTransaction();

        $this->actingAs($this->user)->put(route('transactions.update', $transaction), $this->payload(['account_id' => $foreign->id]))
            ->assertForbidden();

        $this->assertSame(7500, $this->balance($this->account));
        $this->assertSame(10000, $this->balance($foreign));
        $this->assertSame($this->account->id, $transaction->fresh()->account_id);
    }
}
