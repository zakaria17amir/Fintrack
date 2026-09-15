<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Transactions</h2>
            <span class="text-sm text-gray-500">Admin view</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, user name, email" class="w-full text-sm border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                <select name="user_id" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                <select name="category_id" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                <select name="type" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full text-sm border-gray-300 rounded-lg">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>Cleared</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border-gray-300 rounded-lg">
            </div>

            <div class="flex items-end gap-2 lg:col-span-2">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900">Filter</button>
                <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Account</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $transaction->title }}</td>
                            <td class="px-6 py-4">{{ $transaction->user->name }}</td>
                            <td class="px-6 py-4">{{ $transaction->account->name }}</td>
                            <td class="px-6 py-4">{{ $transaction->category->name }}</td>
                            <td class="px-6 py-4">{{ ucfirst($transaction->type) }}</td>
                            <td class="px-6 py-4">{{ ucfirst($transaction->status) }}</td>
                            <td class="px-6 py-4 text-right font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}${{ $transaction->formatted_amount }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>