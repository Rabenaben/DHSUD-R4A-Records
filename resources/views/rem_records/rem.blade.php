<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('REM Records') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('REM Documents Summary')" />

        <div class="space-y-4">
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

    @include('rem_records.partials.rem-modal', ['provinces' => $provinces])
    <x-add-record-modal type="rem" :provinces="$allProvinces" />
    <x-add-file-modal />
    <x-save-file-modal />
    <x-confirm-archive-file-modal />
    <x-confirm-save-record-modal type="REM" />
    <x-export-modal type="rem" :provinces="$provinces" />

</x-app-layout>
