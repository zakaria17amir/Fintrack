<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Budgets</h2>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Budget
            </a>
        </div>
    </x-slot>

    <!-- Month Selector -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-4">
            <label for="month" class="text-sm font-medium text-gray-700">Month:</label>
            <input type="month" id="month" name="month" value="{{ $month }}" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900">Go</button>
        </form>
    </div>

    <!-- Budget Cards -->
    @if($budgets->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($budgets as $budget)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $budget->category->color }}"></div>
                    <h4 class="text-sm font-semibold text-gray-900">{{ $budget->category->name }}</h4>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('budgets.edit', $budget) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Delete this budget?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                    </form>
                </div>
            </div>

            <div class="flex justify-between mb-2">
                <span class="text-sm text-gray-600">${{ number_format($budget->spent / 100, 2) }} spent</span>
                <span class="text-sm font-medium text-gray-900">${{ number_format($budget->amount / 100, 2) }} budget</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div class="h-3 rounded-full transition-all {{ $budget->percentage >= 90 ? 'bg-red-500' : ($budget->percentage >= 70 ? 'bg-yellow-400' : 'bg-blue-600') }}" style="width: {{ $budget->percentage }}%"></div>
            </div>

            <div class="flex justify-between">
                <span class="text-xs text-gray-500">{{ $budget->percentage }}% used</span>
                <span class="text-xs {{ $budget->percentage >= 90 ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                    ${{ number_format(max(0, ($budget->amount - $budget->spent)) / 100, 2) }} remaining
                </span>
            </div>

            @if($budget->notes)
            <p class="mt-3 text-xs text-gray-500 border-t pt-2">{{ $budget->notes }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">No budgets for {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h3>
        <p class="mt-1 text-sm text-gray-500">Create a budget to start tracking your spending.</p>
        <a href="{{ route('budgets.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Create Budget</a>
    </div>
    @endif
</x-app-layout>
