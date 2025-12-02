<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('REM Records') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <div class="relative bg-transparent">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('REM Documents Summary') }}</h2>
            <div class="mt-2 border-b-2 border-gray-600"></div>
        </div>

        <div class="space-y-8">
            <!-- Status Cards -->
            @php $totalDockets = $totalRemDockets; @endphp
            <x-status-cards :totalDockets="$totalDockets" :onShelf="$onShelf" :unavailable="$unavailable" :borrowed="$borrowed" theme="rem" />

            <!-- Folder Container -->
            <div id="folderContainer">
                <div id="folderSectionWrapper">
                    <x-rem.folder-section :provinces="$provinces" />
                </div>
            </div>

        </div>
    </div>

    @include('rem_records.partials.rem-modal')
</x-app-layout>
