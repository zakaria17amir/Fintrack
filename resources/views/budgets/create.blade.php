<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isset($budget) ? 'Edit Budget' : 'Create Budget' }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ isset($budget) ? route('budgets.update', $budget) : route('budgets.store') }}">
                @csrf
                @if(isset($budget)) @method('PATCH') @endif

                <!-- Category (Select) -->
                <div class="mb-5">
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Amount -->
                <div class="mb-5">
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Budget Amount (in cents)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $budget->amount ?? '') }}" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <p class="mt-1 text-xs text-gray-500">Enter amount in cents (e.g., 50000 = $500.00)</p>
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Month (Select) -->
                <div class="mb-5">
                    <label for="month" class="block mb-2 text-sm font-medium text-gray-900">Month</label>
                    <input type="month" id="month" name="month" value="{{ old('month', isset($budget) ? $budget->month->format('Y-m') : now()->format('Y-m')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Notes -->
                <div class="mb-5">
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('notes', $budget->notes ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">{{ isset($budget) ? 'Update Budget' : 'Create Budget' }}</button>
                    <a href="{{ route('budgets.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
