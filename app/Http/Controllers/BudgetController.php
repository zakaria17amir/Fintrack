<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $month = $request->get('month', now()->format('Y-m'));

        $budgets = $user->budgets()
            ->with('category')
            ->whereDate('month', $month . '-01')
            ->get();

        return view('budgets.index', compact('budgets', 'month'));
    }

    public function create()
    {
        $categories = Category::whereIn('type', ['expense', 'both'])->orderBy('name')->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['month'] = $data['month'] . '-01';

        Budget::create($data);

        return redirect()->route('budgets.index', ['month' => $request->month])
            ->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);

        $categories = Category::whereIn('type', ['expense', 'both'])->orderBy('name')->get();

        return view('budgets.create', compact('budget', 'categories'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $data = $request->validated();
        $data['month'] = $data['month'] . '-01';

        $budget->update($data);

        return redirect()->route('budgets.index', ['month' => $request->month])
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);

        $month = $budget->month->format('Y-m');
        $budget->delete();

        return redirect()->route('budgets.index', ['month' => $month])
            ->with('success', 'Budget deleted successfully.');
    }
}
