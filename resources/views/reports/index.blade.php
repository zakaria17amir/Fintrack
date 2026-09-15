<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('reports') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 w-full">Apply Filters</button>
                </div>
            </div>

            <!-- Category Checkboxes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Categories</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($categories as $category)
                    <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300" {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <p class="text-sm text-gray-500">Total Income</p>
            <p class="text-2xl font-bold text-green-600">${{ number_format($totalIncome / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <p class="text-sm text-gray-500">Total Expenses</p>
            <p class="text-2xl font-bold text-red-600">${{ number_format($totalExpense / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <p class="text-sm text-gray-500">Net</p>
            <p class="text-2xl font-bold {{ ($totalIncome - $totalExpense) >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format(($totalIncome - $totalExpense) / 100, 2) }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Spending by Category</h3>
            @if($categoryBreakdown->count() > 0)
            <div class="flex justify-center">
                <canvas id="categoryPieChart" style="max-height: 350px;"></canvas>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No expense data for the selected period.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trend</h3>
            <canvas id="monthlyTrendChart" style="max-height: 350px;"></canvas>
        </div>
    </div>

    <!-- Category Breakdown Table -->
    @if($categoryBreakdown->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Category Breakdown</h3>
        </div>
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                    <th class="px-6 py-3 text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryBreakdown->sortByDesc('total') as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $item->category->color }}"></div>
                            <span class="font-medium text-gray-900">{{ $item->category->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold">${{ number_format($item->total / 100, 2) }}</td>
                    <td class="px-6 py-4 text-right">{{ $totalExpense > 0 ? number_format(($item->total / $totalExpense) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if($categoryBreakdown->count() > 0)
        new Chart(document.getElementById('categoryPieChart'), {
            type: 'pie',
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
                plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
            }
        });
        @endif

        new Chart(document.getElementById('monthlyTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend->pluck('month')) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($monthlyTrend->pluck('income')->map(fn($v) => $v / 100)) !!},
                        borderColor: '#10B981',
                        backgroundColor: '#10B98120',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($monthlyTrend->pluck('expense')->map(fn($v) => $v / 100)) !!},
                        borderColor: '#EF4444',
                        backgroundColor: '#EF444420',
                        fill: true,
                        tension: 0.3
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
