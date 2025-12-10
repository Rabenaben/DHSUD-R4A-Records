<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Accounts') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('List of Accounts')" />

        <!-- Filters, Search, and Add User -->
        @include('accounts.partials.accounts-table')
    </div>
    
    @include('accounts.partials.add-user')
    @include('accounts.partials.edit-user')

    @if (session('success'))
        <script>
            function showToastOnLoad() {
                const toast = document.getElementById('toast');
                if (toast) {
                    showToast('{{ session('success') }}', 'success');
                } else {
                    setTimeout(showToastOnLoad, 100);
                }
            }
            document.addEventListener('DOMContentLoaded', showToastOnLoad);
        </script>
    @endif

</x-app-layout>
