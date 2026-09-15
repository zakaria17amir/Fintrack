<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalBalance / 100, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monthly Income</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($monthlyIncome / 100, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monthly Expenses</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($monthlyExpense / 100, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Net This Month</p>
                    <p class="text-2xl font-bold {{ ($monthlyIncome - $monthlyExpense) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format(($monthlyIncome - $monthlyExpense) / 100, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Spending by Category -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Spending by Category</h3>
            @if($categoryBreakdown->count() > 0)
            <div class="flex justify-center">
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No expense data for this month yet.</p>
            @endif
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Income vs Expenses (6 Months)</h3>
            <canvas id="trendChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Recent Transactions & Budget Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            @forelse($recentTransactions as $transaction)
            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        @if($transaction->type === 'income')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $transaction->title }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->category->name }} &middot; {{ $transaction->transaction_date->format('M d') }}</p>
                    </div>
                </div>
                <span class="text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}${{ $transaction->formatted_amount }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No transactions yet.</p>
            @endforelse
        </div>

        <!-- Budget Progress -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Budget Progress</h3>
                <a href="{{ route('budgets.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            @forelse($budgets as $budget)
            <div class="mb-4">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">{{ $budget->category->name }}</span>
                    <span class="text-sm text-gray-500">${{ number_format($budget->spent / 100, 2) }} / ${{ number_format($budget->amount / 100, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $budget->percentage >= 90 ? 'bg-red-600' : ($budget->percentage >= 70 ? 'bg-yellow-400' : 'bg-blue-600') }}" style="width: {{ $budget->percentage }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No budgets set for this month.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if($categoryBreakdown->count() > 0)
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryBreakdown->pluck('category.name')) !!},
                datasets: [{
                    data: {!! json_encode($categoryBreakdown->pluck('total')->map(fn($v) => $v / 100)) !!},
                    backgroundColor: {!! json_encode($categoryBreakdown->pluck('category.color')) !!},
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } } }
            }
        });
        @endif

        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyTrend->pluck('month')) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($monthlyTrend->pluck('income')->map(fn($v) => $v / 100)) !!},
                        backgroundColor: '#10B981',
                        borderRadius: 4
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($monthlyTrend->pluck('expense')->map(fn($v) => $v / 100)) !!},
                        backgroundColor: '#EF4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
    @endpush
</x-app-layout>
