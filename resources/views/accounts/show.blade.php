<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $account->color }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $account->name }}</h2>
                    <p class="text-sm text-gray-500">{{ str_replace('_', ' ', ucfirst($account->type)) }} &middot; {{ $account->currency }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($permission === 'owner' || $permission === 'edit')
                <a href="{{ route('transactions.create', ['account_id' => $account->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">Add Transaction</a>
                @endif
                @if($permission === 'owner')
                <a href="{{ route('accounts.edit', $account) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">Edit</a>
                <a href="{{ route('accounts.sharing.index', $account) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">Sharing</a>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Balance Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <p class="text-sm text-gray-500">Current Balance</p>
        <p class="text-3xl font-bold {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
            {{ $account->currency }} {{ number_format($account->balance / 100, 2) }}
        </p>
        @if($permission !== 'owner')
        <p class="mt-2 text-sm text-purple-600">Shared with you ({{ $permission }} access) by {{ $account->owner->name }}</p>
        @endif
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $transaction->title }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->type === 'income' ? 'bg-green-100 text-green-700' : ($transaction->type === 'expense' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->status === 'cleared' ? 'bg-green-100 text-green-700' : ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}${{ $transaction->formatted_amount }}
                        </td>
                        <td class="px-6 py-4">
                            @if($permission === 'owner' || $permission === 'edit')
                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No transactions found for this account.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
