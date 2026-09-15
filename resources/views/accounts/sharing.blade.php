<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Sharing: {{ $account->name }}</h2>
    </x-slot>

    <div class="max-w-3xl">
        <!-- Current Shared Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Shared Users</h3>
            @if($sharedUsers->count() > 0)
            <div class="space-y-3">
                @foreach($sharedUsers as $user)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Update Permission -->
                        <form method="POST" action="{{ route('accounts.sharing.update', [$account, $user]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="permission" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="view" {{ $user->pivot->permission === 'view' ? 'selected' : '' }}>View Only</option>
                                <option value="edit" {{ $user->pivot->permission === 'edit' ? 'selected' : '' }}>Can Edit</option>
                            </select>
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Update</button>
                        </form>
                        <!-- Remove -->
                        <form method="POST" action="{{ route('accounts.sharing.destroy', [$account, $user]) }}" onsubmit="return confirm('Remove access for {{ $user->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700">Remove</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">This account is not shared with anyone yet.</p>
            @endif
        </div>

        <!-- Add New Shared Users (Checkbox List) -->
        @if($availableUsers->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Share With Users</h3>
            <form method="POST" action="{{ route('accounts.sharing.store', $account) }}">
                @csrf

                <!-- Checkbox list of users -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Select Users</label>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($availableUsers as $user)
                        <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('user_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Permission (Select) -->
                <div class="mb-5">
                    <label for="permission" class="block mb-2 text-sm font-medium text-gray-900">Permission Level</label>
                    <select id="permission" name="permission" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="view">View Only - Can see account and transactions</option>
                        <option value="edit">Can Edit - Can create, update, delete transactions</option>
                    </select>
                    @error('permission') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white font-medium rounded-lg text-sm hover:bg-purple-700">Share Account</button>
            </form>
        </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('accounts.show', $account) }}" class="text-sm text-gray-600 hover:underline">&larr; Back to Account</a>
        </div>
    </div>
</x-app-layout>
