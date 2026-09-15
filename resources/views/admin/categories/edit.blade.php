<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Category: {{ $category->name }}</h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="slug" class="block mb-2 text-sm font-medium text-gray-900">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Type</label>
                    <div class="flex gap-4">
                        @foreach(['income' => 'Income', 'expense' => 'Expense', 'both' => 'Both'] as $value => $label)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 flex-1 justify-center {{ old('type', $category->type) === $value ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="{{ $value }}" class="w-4 h-4 text-blue-600" {{ old('type', $category->type) === $value ? 'checked' : '' }} required>
                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-5">
                    <label for="color" class="block mb-2 text-sm font-medium text-gray-900">Color</label>
                    <input type="color" id="color" name="color" value="{{ old('color', $category->color) }}" class="h-10 w-20 rounded border border-gray-300">
                </div>

                <div class="mb-5">
                    <label for="icon" class="block mb-2 text-sm font-medium text-gray-900">Icon</label>
                    <select id="icon" name="icon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach(['tag', 'banknotes', 'briefcase', 'chart-bar', 'gift', 'utensils', 'car', 'home', 'bolt', 'film', 'shopping-bag', 'heart', 'book', 'plane', 'shield', 'piggy-bank', 'ellipsis'] as $icon)
                        <option value="{{ $icon }}" {{ old('icon', $category->icon) === $icon ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $icon)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="flex items-center">
                        <input type="hidden" name="is_system" value="0">
                        <input type="checkbox" name="is_system" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_system', $category->is_system) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-medium text-gray-900">System Category</span>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>

            @if($category->transactions()->count() === 0)
            <div class="mt-8 pt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg text-sm hover:bg-red-700">Delete Category</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
