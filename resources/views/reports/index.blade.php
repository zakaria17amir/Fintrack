<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    {{-- React + TypeScript island (resources/js/reports). @json escapes quotes and tags for the attribute. --}}
    <div id="reports-root" data-initial='@json($initial)'></div>

    @push('scripts')
        @viteReactRefresh
        @vite('resources/js/reports/main.tsx')
    @endpush
</x-app-layout>
