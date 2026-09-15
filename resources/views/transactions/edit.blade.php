<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Transaction: {{ $transaction->title }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label for="account_id" class="block mb-2 text-sm font-medium text-gray-900">Account</label>
                    <select id="account_id" name="account_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} ({{ $account->currency }})
                        </option>
                        @endforeach
                    </select>
                    @error('account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Transaction Type</label>
                    <div class="flex gap-4">
                        @foreach(['income' => 'Income', 'expense' => 'Expense'] as $value => $label)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 justify-center {{ old('type', $transaction->type) === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="{{ $value }}" class="w-4 h-4 text-blue-600" {{ old('type', $transaction->type) === $value ? 'checked' : '' }} required>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-5">
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $transaction->title) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Amount (in cents)</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach(['cleared' => 'Cleared', 'pending' => 'Pending', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $transaction->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="transaction_date" class="block mb-2 text-sm font-medium text-gray-900">Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->toDateString()) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <div class="mb-5">
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('notes', $transaction->notes) }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="receipt" class="block mb-2 text-sm font-medium text-gray-900">Receipt</label>
                    @if($transaction->receipt_path)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                        <span class="text-sm text-gray-600">Current: {{ basename($transaction->receipt_path) }}</span>
                        <a href="{{ asset('storage/' . $transaction->receipt_path) }}" target="_blank" class="text-sm text-blue-600 hover:underline">View</a>
                    </div>
                    @endif
                    <input type="file" id="receipt" name="receipt" accept="image/*,.pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2">
                    <p class="mt-1 text-xs text-gray-500">Upload a new file to replace the current receipt.</p>
                    @error('receipt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="flex items-center">
                        <input type="hidden" name="is_recurring" value="0">
                        <input type="checkbox" name="is_recurring" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_recurring', $transaction->is_recurring) ? 'checked' : '' }} onchange="document.getElementById('recurring_interval_group').classList.toggle('hidden', !this.checked)">
                        <span class="ml-2 text-sm font-medium text-gray-900">Recurring Transaction</span>
                    </label>
                </div>

                <div id="recurring_interval_group" class="mb-5 {{ old('is_recurring', $transaction->is_recurring) ? '' : 'hidden' }}">
                    <label for="recurring_interval" class="block mb-2 text-sm font-medium text-gray-900">Recurring Interval</label>
                    <select id="recurring_interval" name="recurring_interval" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Select interval</option>
                        @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label)
                        <option value="{{ $value }}" {{ old('recurring_interval', $transaction->recurring_interval) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">Update Transaction</button>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg text-sm hover:bg-red-700">Delete Transaction</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
