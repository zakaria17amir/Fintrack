<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Account</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('accounts.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-5">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Account Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Type (Radio Buttons) -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Account Type</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(['bank' => 'Bank', 'cash' => 'Cash', 'credit_card' => 'Credit Card', 'savings' => 'Savings', 'investment' => 'Investment'] as $value => $label)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('type') === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="{{ $value }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500" {{ old('type') === $value ? 'checked' : '' }} required>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Currency (Select) -->
                <div class="mb-5">
                    <label for="currency" class="block mb-2 text-sm font-medium text-gray-900">Currency</label>
                    <select id="currency" name="currency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        <option value="HUF" {{ old('currency') === 'HUF' ? 'selected' : '' }}>HUF - Hungarian Forint</option>
                    </select>
                    @error('currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Balance -->
                <div class="mb-5">
                    <label for="balance" class="block mb-2 text-sm font-medium text-gray-900">Initial Balance (in cents)</label>
                    <input type="number" id="balance" name="balance" value="{{ old('balance', 0) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    <p class="mt-1 text-xs text-gray-500">Enter amount in cents (e.g., 100000 = $1,000.00)</p>
                    @error('balance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Color -->
                <div class="mb-5">
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-900">Color</label>
                    <input type="color" id="color" name="color" value="{{ old('color', '#3B82F6') }}" class="h-10 w-20 rounded border border-gray-300">
                    @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Icon -->
                <div class="mb-5">
                    <label for="icon" class="block mb-2 text-sm font-medium text-gray-900">Icon</label>
                    <select id="icon" name="icon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="wallet" {{ old('icon', 'wallet') === 'wallet' ? 'selected' : '' }}>Wallet</option>
                        <option value="bank" {{ old('icon') === 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="credit-card" {{ old('icon') === 'credit-card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="piggy-bank" {{ old('icon') === 'piggy-bank' ? 'selected' : '' }}>Piggy Bank</option>
                        <option value="chart-line" {{ old('icon') === 'chart-line' ? 'selected' : '' }}>Chart Line</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700 transition">Create Account</button>
                    <a href="{{ route('accounts.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
