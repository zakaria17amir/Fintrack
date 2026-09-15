<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Account: {{ $account->name }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('accounts.update', $account) }}">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Account Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Account Type</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(['bank' => 'Bank', 'cash' => 'Cash', 'credit_card' => 'Credit Card', 'savings' => 'Savings', 'investment' => 'Investment'] as $value => $label)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('type', $account->type) === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="{{ $value }}" class="w-4 h-4 text-blue-600" {{ old('type', $account->type) === $value ? 'checked' : '' }} required>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="currency" class="block mb-2 text-sm font-medium text-gray-900">Currency</label>
                    <select id="currency" name="currency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach(['USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'HUF' => 'HUF - Hungarian Forint'] as $value => $label)
                        <option value="{{ $value }}" {{ old('currency', $account->currency) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="balance" class="block mb-2 text-sm font-medium text-gray-900">Balance (in cents)</label>
                    <input type="number" id="balance" name="balance" value="{{ old('balance', $account->balance) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                </div>

                <div class="mb-5">
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-900">Color</label>
                    <input type="color" id="color" name="color" value="{{ old('color', $account->color) }}" class="h-10 w-20 rounded border border-gray-300">
                </div>

                <div class="mb-5">
                    <label for="icon" class="block mb-2 text-sm font-medium text-gray-900">Icon</label>
                    <select id="icon" name="icon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach(['wallet' => 'Wallet', 'bank' => 'Bank', 'credit-card' => 'Credit Card', 'piggy-bank' => 'Piggy Bank', 'chart-line' => 'Chart Line'] as $value => $label)
                        <option value="{{ $value }}" {{ old('icon', $account->icon) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-medium text-gray-900">Active</span>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">Update Account</button>
                    <a href="{{ route('accounts.show', $account) }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

            <!-- Delete -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Are you sure you want to delete this account? All transactions will also be deleted.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg text-sm hover:bg-red-700">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
