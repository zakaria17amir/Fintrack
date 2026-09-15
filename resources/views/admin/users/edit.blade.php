<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Role (Radio) -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                    <div class="flex gap-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 {{ old('role', $user->role) === 'user' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="role" value="user" class="w-4 h-4 text-blue-600" {{ old('role', $user->role) === 'user' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium text-gray-900">User</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 {{ old('role', $user->role) === 'admin' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" name="role" value="admin" class="w-4 h-4 text-red-600" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium text-gray-900">Admin</span>
                        </label>
                    </div>
                    @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Active -->
                <div class="mb-5">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-medium text-gray-900">Active Account</span>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">Update User</button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>

        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">User Details</h3>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="text-gray-500">Joined</dt>
                <dd class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                <dt class="text-gray-500">Phone</dt>
                <dd class="text-gray-900">{{ $user->phone ?? 'Not set' }}</dd>
                <dt class="text-gray-500">Bio</dt>
                <dd class="text-gray-900">{{ $user->bio ?? 'Not set' }}</dd>
            </dl>
        </div>
    </div>
</x-app-layout>
