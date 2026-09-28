<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSharingPermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $viewer;

    private User $editor;

    private User $stranger;

    private Account $account;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->owner, $this->viewer, $this->editor, $this->stranger] = User::factory()->count(4)->create()->all();
        $this->account = Account::factory()->create(['user_id' => $this->owner->id, 'balance' => 10000]);
        $this->account->sharedUsers()->attach($this->viewer->id, ['permission' => 'view']);
        $this->account->sharedUsers()->attach($this->editor->id, ['permission' => 'edit']);
        $this->category = Category::factory()->create();
    }

    private function expense(array $overrides = []): array
    {
        return array_merge([
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'title' => 'Rent',
            'amount' => 2500,
            'type' => 'expense',
            'status' => 'cleared',
            'transaction_date' => '2026-09-01',
        ], $overrides);
    }

    private function balance(): int
    {
        return $this->account->fresh()->balance;
    }

    public function test_viewer_can_see_the_shared_account(): void
    {
        $this->actingAs($this->viewer)->get(route('accounts.show', $this->account))->assertOk();
    }

    public function test_viewer_cannot_add_transactions_to_the_shared_account(): void
    {
        $this->actingAs($this->viewer)->post(route('transactions.store'), $this->expense())->assertForbidden();

        $this->assertSame(10000, $this->balance());
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_editor_can_add_transactions_to_the_shared_account(): void
    {
        $this->actingAs($this->editor)->post(route('transactions.store'), $this->expense())
            ->assertRedirect(route('transactions.index'));

        $this->assertSame(7500, $this->balance());
    }

    public function test_viewer_cannot_edit_a_transaction_on_the_shared_account(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->owner->id, 'account_id' => $this->account->id, 'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->viewer)->put(route('transactions.update', $transaction), $this->expense(['amount' => 1]))
            ->assertForbidden();
    }

    public function test_editor_moving_a_transaction_off_the_shared_account_updates_both_balances_once(): void
    {
        $own = Account::factory()->create(['user_id' => $this->editor->id, 'balance' => 10000]);
        $this->actingAs($this->editor)->post(route('transactions.store'), $this->expense());
        $transaction = Transaction::latest('id')->firstOrFail();

        $this->actingAs($this->editor)->put(route('transactions.update', $transaction), $this->expense(['account_id' => $own->id]))
            ->assertRedirect(route('transactions.index'));

        $this->assertSame(10000, $this->balance());
        $this->assertSame(7500, $own->fresh()->balance);
    }

    public function test_stranger_cannot_see_the_account(): void
    {
        $this->actingAs($this->stranger)->get(route('accounts.show', $this->account))->assertForbidden();
    }

    public function test_stranger_cannot_add_transactions_to_the_account(): void
    {
        $this->actingAs($this->stranger)->post(route('transactions.store'), $this->expense())->assertForbidden();

        $this->assertSame(10000, $this->balance());
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_only_the_owner_manages_sharing(): void
    {
        $this->actingAs($this->editor)->get(route('accounts.sharing.index', $this->account))->assertForbidden();
        $this->actingAs($this->owner)->get(route('accounts.sharing.index', $this->account))->assertOk();
    }

    public function test_an_account_cannot_be_shared_with_its_owner(): void
    {
        $this->actingAs($this->owner)
            ->post(route('accounts.sharing.store', $this->account), ['user_ids' => [$this->owner->id], 'permission' => 'view'])
            ->assertSessionHasErrors('user_ids');
    }
}
