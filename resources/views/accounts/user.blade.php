<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Accounts') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <div class="relative bg-transparent">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('List of Accounts') }}</h2>
            <div class="mt-2 border-b-2 border-gray-700"></div>
        </div>

        @if (session('success'))
            <div class="relative mt-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filters, Search, and Add User -->
        @include('accounts.partials.accounts-table')
    </div>
    
    @include('accounts.partials.add-user')
    @include('accounts.partials.edit-user')
    
</x-app-layout>
