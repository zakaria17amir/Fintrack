<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaction Details</h2>
            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-blue-600 hover:underline">Back to all transactions</a>
        </div>
    </x-slot>

    <div class="max-w-4xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Title</p>
                <p class="text-base font-medium text-gray-900">{{ $transaction->title }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">User</p>
                <p class="text-base font-medium text-gray-900">{{ $transaction->user->name }} ({{ $transaction->user->email }})</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Account</p>
                <p class="text-base font-medium text-gray-900">{{ $transaction->account->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Category</p>
                <p class="text-base font-medium text-gray-900">{{ $transaction->category->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="text-base font-medium text-gray-900">{{ ucfirst($transaction->type) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="text-base font-medium text-gray-900">{{ ucfirst($transaction->status) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Amount</p>
                <p class="text-base font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}${{ $transaction->formatted_amount }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="text-base font-medium text-gray-900">{{ $transaction->transaction_date->format('M d, Y') }}</p>
            </div>
        </div>

        @if($transaction->notes)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-sm text-gray-900">{{ $transaction->notes }}</p>
            </div>
        @endif

        @if($transaction->receipt_path)
            <div class="mt-6">
                <p class="text-sm text-gray-500 mb-2">Receipt</p>
                <a href="{{ asset('storage/' . $transaction->receipt_path) }}" target="_blank" class="text-blue-600 hover:underline">View Receipt</a>
            </div>
        @endif
    </div>
</x-app-layout>