<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Category Management</h2>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Category
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Slug</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">System</th>
                        <th class="px-6 py-3">Transactions</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $category->slug }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $category->type === 'income' ? 'bg-green-100 text-green-700' : ($category->type === 'expense' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->is_system)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">System</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $category->transactions_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:underline">Edit</a>
                                @if($category->transactions_count === 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete category {{ $category->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                                @else
                                <span class="text-gray-400 text-xs" title="Cannot delete: has transactions">Protected</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
