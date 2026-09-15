<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Accounts</h2>
            <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Account
            </a>
        </div>
    </x-slot>

    <!-- Own Accounts -->
    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Accounts</h3>
    @if($ownAccounts->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach($ownAccounts as $account)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $account->color }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $account->name }}</h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ str_replace('_', ' ', ucfirst($account->type)) }}</span>
                    </div>
                </div>
                @if(!$account->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                @endif
            </div>
            <p class="text-2xl font-bold {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                {{ $account->currency }} {{ number_format($account->balance / 100, 2) }}
            </p>
            <div class="flex items-center gap-2 mt-4">
                <a href="{{ route('accounts.show', $account) }}" class="text-sm text-blue-600 hover:underline">View</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('accounts.edit', $account) }}" class="text-sm text-gray-600 hover:underline">Edit</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('accounts.sharing.index', $account) }}" class="text-sm text-purple-600 hover:underline">Sharing</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center mb-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">No accounts</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating your first account.</p>
        <a href="{{ route('accounts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Create Account</a>
    </div>
    @endif

    <!-- Shared Accounts -->
    @if($sharedAccounts->count() > 0)
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shared With Me</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($sharedAccounts as $account)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 border-l-4 border-l-purple-400">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $account->color }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $account->name }}</h4>
                        <p class="text-xs text-gray-500">Owner: {{ $account->owner->name }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $account->pivot->permission === 'edit' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($account->pivot->permission) }}
                </span>
            </div>
            <p class="text-2xl font-bold {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                {{ $account->currency }} {{ number_format($account->balance / 100, 2) }}
            </p>
            <div class="mt-4">
                <a href="{{ route('accounts.show', $account) }}" class="text-sm text-blue-600 hover:underline">View</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
