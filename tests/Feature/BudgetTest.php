<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_storing_a_budget_saves_the_first_day_of_the_month(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'expense']);

        $this->actingAs($user)
            ->post(route('budgets.store'), ['category_id' => $category->id, 'amount' => 50000, 'month' => '2026-09'])
            ->assertRedirect(route('budgets.index', ['month' => '2026-09']));

        $budget = Budget::firstOrFail();
        $this->assertSame($user->id, $budget->user_id);
        $this->assertSame('2026-09-01', $budget->month->toDateString());
        $this->assertSame(50000, (int) $budget->amount);
    }

    public function test_another_user_cannot_update_or_delete_a_budget(): void
    {
        $budget = Budget::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->put(route('budgets.update', $budget), ['category_id' => $budget->category_id, 'amount' => 1, 'month' => '2026-09'])
            ->assertForbidden();
        $this->actingAs($other)->delete(route('budgets.destroy', $budget))->assertForbidden();

        $this->assertModelExists($budget);
    }

    public function test_index_lists_only_the_users_budgets_for_the_month(): void
    {
        $user = User::factory()->create();
        Budget::factory()->create(['user_id' => $user->id, 'month' => '2026-09-01']);
        Budget::factory()->create(['user_id' => $user->id, 'month' => '2026-08-01']);
        Budget::factory()->create(['month' => '2026-09-01']);

        $this->actingAs($user)->get(route('budgets.index', ['month' => '2026-09']))
            ->assertOk()
            ->assertViewHas('budgets', fn ($budgets) => $budgets->count() === 1);
    }
}
