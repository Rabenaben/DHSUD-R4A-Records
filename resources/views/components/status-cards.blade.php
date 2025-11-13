@php
    $baseColors = [
        'total' => 'bg-linear-to-br from-gray-600 to-gray-900 border-gray-300 border-2',
        'onShelf' => 'bg-linear-to-br from-green-400 to-green-700 border-gray-300 border-2',
        'unavailable' => 'bg-linear-to-br from-yellow-400 to-yellow-700 border-gray-300 border-2',
        'borrowed' => 'bg-linear-to-br from-red-500 to-red-800 border-gray-300 border-2',
    ];

    $textColors =
        ($theme ?? '') === 'rem'
            ? [
                'total' => 'text-white',
                'onShelf' => 'text-white',
                'unavailable' => 'text-slate-800',
                'borrowed' => 'text-white',
            ]
            : [
                'total' => 'text-zinc-100',
                'onShelf' => 'text-zinc-100',
                'unavailable' => 'text-slate-800',
                'borrowed' => 'text-zinc-100',
            ];

    // Merge while keeping keys
    $colors = [];
    foreach ($baseColors as $key => $value) {
        $colors[$key] = $value . ' ' . $textColors[$key];
    }
@endphp

<div class="mt-2 flex flex-wrap gap-2">
    <!-- Total Dockets -->
    <div class="{{ $colors['total'] }} flex-1 rounded-lg p-4 font-medium shadow">
        <div class="flex items-center justify-between">
            <div class="text-left">
                <p class="text-sm font-semibold">Total Dockets</p>
                <h2 class="text-2x1 mt-1 font-bold tracking-wider">{{ $totalDockets }}</h2>
            </div>
            <div class="shrink-0 text-4xl">
                <i class="bi bi-folder2-open" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- On-Shelf -->
    <div class="{{ $colors['onShelf'] }} flex-1 rounded-lg p-4 font-medium shadow">
        <div class="flex items-center justify-between">
            <div class="text-left">
                <p class="text-sm font-semibold">On-Shelf</p>
                <h2 class="text-2x1 mt-1 font-bold tracking-wider">{{ $onShelf }}</h2>
            </div>
            <div class="shrink-0 text-4xl">
                <i class="bi bi-archive-fill" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- Unavailable -->
    <div class="{{ $colors['unavailable'] }} flex-1 rounded-lg p-4 font-medium shadow">
        <div class="flex items-center justify-between">
            <div class="text-left">
                <p class="text-sm font-semibold">Unavailable</p>
                <h2 class="text-2x1 mt-1 font-bold tracking-wider">{{ $unavailable }}</h2>
            </div>
            <div class="shrink-0 text-4xl">
                <i class="bi bi-file-earmark-x-fill" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- Borrowed -->
    <div class="{{ $colors['borrowed'] }} flex-1 rounded-lg p-4 font-medium shadow">
        <div class="flex items-center justify-between">
            <div class="text-left">
                <p class="text-sm font-semibold">Borrowed</p>
                <h2 class="text-2x1 mt-1 font-bold tracking-wider">{{ $borrowed }}</h2>
            </div>
            <div class="shrink-0 text-4xl">
                <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>
