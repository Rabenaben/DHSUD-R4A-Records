<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Accounts') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Section Header Card -->
        <div class="relative bg-transparent py-4">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('List of Accounts') }}</h2>
            <div class="mt-2 border-b-2 border-gray-600"></div>
        </div>

    </div>
</x-app-layout>
