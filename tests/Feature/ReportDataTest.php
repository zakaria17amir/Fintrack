<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportDataTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Account $account;

    private Category $food;

    private Category $salary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id]);
        $this->food = Category::factory()->create(['name' => 'Food', 'color' => '#EF4444']);
        $this->salary = Category::factory()->create(['name' => 'Salary', 'color' => '#10B981']);
    }

    private function tx(array $attributes): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'status' => 'cleared',
            ...$attributes,
        ]);
    }

    private function data(array $query = [])
    {
        return $this->actingAs($this->user)->getJson(route('reports.data', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-09-30',
            ...$query,
        ]));
    }

    public function test_returns_totals_category_breakdown_and_zero_filled_months_in_cents(): void
    {
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 3000, 'transaction_date' => '2026-09-10']);
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 1500, 'transaction_date' => '2026-07-02']);
        $this->tx(['category_id' => $this->salary->id, 'type' => 'income', 'amount' => 10000, 'transaction_date' => '2026-09-01']);

        $response = $this->data()->assertOk();

        $response->assertJsonPath('totals', ['income' => 10000, 'expense' => 4500, 'net' => 5500]);
        $response->assertJsonPath('byCategory', [
            ['id' => $this->food->id, 'name' => 'Food', 'color' => '#EF4444', 'total' => 4500],
        ]);
        $response->assertJsonPath('monthly', [
            ['month' => '2026-04', 'income' => 0, 'expense' => 0],
            ['month' => '2026-05', 'income' => 0, 'expense' => 0],
            ['month' => '2026-06', 'income' => 0, 'expense' => 0],
            ['month' => '2026-07', 'income' => 0, 'expense' => 1500],
            ['month' => '2026-08', 'income' => 0, 'expense' => 0],
            ['month' => '2026-09', 'income' => 10000, 'expense' => 3000],
        ]);
    }

    public function test_excludes_pending_other_users_and_out_of_range_transactions(): void
    {
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 3000, 'transaction_date' => '2026-09-10']);
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 5000, 'transaction_date' => '2026-09-10', 'status' => 'pending']);
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 900, 'transaction_date' => '2026-03-31']);
        $other = User::factory()->create();
        Transaction::factory()->create([
            'user_id' => $other->id,
            'account_id' => Account::factory()->create(['user_id' => $other->id])->id,
            'category_id' => $this->food->id, 'type' => 'expense', 'amount' => 7000,
            'status' => 'cleared', 'transaction_date' => '2026-09-10',
        ]);

        $this->data()->assertOk()->assertJsonPath('totals.expense', 3000);
    }

    public function test_category_filter_limits_every_figure(): void
    {
        $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 3000, 'transaction_date' => '2026-09-10']);
        $this->tx(['category_id' => $this->salary->id, 'type' => 'income', 'amount' => 10000, 'transaction_date' => '2026-09-01']);

        $this->data(['categories' => [$this->salary->id]])
            ->assertOk()
            ->assertJsonPath('totals', ['income' => 10000, 'expense' => 0, 'net' => 10000])
            ->assertJsonPath('byCategory', []);
    }

    public function test_rejects_an_end_date_before_the_start_date(): void
    {
        $this->data(['date_from' => '2026-09-30', 'date_to' => '2026-09-01'])
            ->assertStatus(422)->assertJsonValidationErrors('date_to');
    }

    public function test_rejects_malformed_dates_and_unknown_categories(): void
    {
        $this->data(['date_from' => 'garbage'])->assertStatus(422)->assertJsonValidationErrors('date_from');
        $this->data(['categories' => [999999]])->assertStatus(422)->assertJsonValidationErrors('categories.0');
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('reports.data'))->assertRedirect(route('login'));
    }

    public function test_a_six_month_report_uses_a_fixed_number_of_queries(): void
    {
        foreach (range(4, 9) as $month) {
            $this->tx(['category_id' => $this->food->id, 'type' => 'expense', 'amount' => 100, 'transaction_date' => "2026-0{$month}-05"]);
        }
        $this->actingAs($this->user);

        DB::enableQueryLog();
        $this->data()->assertOk();

        // Totals, category breakdown and monthly trend: one grouped query each, plus auth.
        $this->assertLessThanOrEqual(5, count(DB::getQueryLog()));
    }
}
