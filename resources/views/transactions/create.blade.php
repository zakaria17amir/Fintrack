<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Transaction</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Account (Select) -->
                <div class="mb-5">
                    <label for="account_id" class="block mb-2 text-sm font-medium text-gray-900">Account</label>
                    <select id="account_id" name="account_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select an account</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('account_id', request('account_id')) == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} ({{ $account->currency }})
                        </option>
                        @endforeach
                    </select>
                    @error('account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Type (Radio Buttons) -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Transaction Type</label>
                    <div class="flex gap-4">
                        @foreach(['income' => 'Income', 'expense' => 'Expense'] as $value => $label)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 justify-center {{ old('type') === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="{{ $value }}" class="w-4 h-4 text-blue-600" {{ old('type') === $value ? 'checked' : '' }} required>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Title -->
                <div class="mb-5">
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Amount -->
                <div class="mb-5">
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Amount (in cents)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <p class="mt-1 text-xs text-gray-500">Enter amount in cents (e.g., 5000 = $50.00)</p>
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Category (Select) -->
                <div class="mb-5">
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->type }})
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Status (Select) -->
                <div class="mb-5">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="cleared" {{ old('status', 'cleared') === 'cleared' ? 'selected' : '' }}>Cleared</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Date -->
                <div class="mb-5">
                    <label for="transaction_date" class="block mb-2 text-sm font-medium text-gray-900">Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('transaction_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Notes -->
                <div class="mb-5">
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('notes') }}</textarea>
                </div>

                <!-- Receipt Upload (File) -->
                <div class="mb-5">
                    <label for="receipt" class="block mb-2 text-sm font-medium text-gray-900">Receipt (optional)</label>
                    <input type="file" id="receipt" name="receipt" accept="image/*,.pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, or PDF. Max 2MB.</p>
                    @error('receipt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Recurring -->
                <div class="mb-5">
                    <label class="flex items-center">
                        <input type="hidden" name="is_recurring" value="0">
                        <input type="checkbox" name="is_recurring" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_recurring') ? 'checked' : '' }} onchange="document.getElementById('recurring_interval_group').classList.toggle('hidden', !this.checked)">
                        <span class="ml-2 text-sm font-medium text-gray-900">Recurring Transaction</span>
                    </label>
                </div>

                <div id="recurring_interval_group" class="mb-5 {{ old('is_recurring') ? '' : 'hidden' }}">
                    <label for="recurring_interval" class="block mb-2 text-sm font-medium text-gray-900">Recurring Interval</label>
                    <select id="recurring_interval" name="recurring_interval" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Select interval</option>
                        <option value="daily" {{ old('recurring_interval') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('recurring_interval') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('recurring_interval') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('recurring_interval') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">Create Transaction</button>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
