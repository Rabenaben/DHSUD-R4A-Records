@php
    $baseColors = [
        'total' => 'from-gray-600 to-gray-900',
        'onShelf' => 'from-green-400 to-green-700',
        'unavailable' => 'from-red-500 to-red-800',
        'borrowed' => 'from-yellow-400 to-yellow-700',
    ];

    $cards = [
        'total' => ['label' => 'Total Dockets', 'value' => $totalDockets, 'icon' => 'bi-folder2-open'],
        'onShelf' => ['label' => 'On-Shelf', 'value' => $onShelf, 'icon' => 'bi-archive-fill'],
        'unavailable' => ['label' => 'Unavailable', 'value' => $unavailable, 'icon' => 'bi-file-earmark-x-fill'],
        'borrowed' => ['label' => 'Borrowed', 'value' => $borrowed, 'icon' => 'bi-arrow-left-right'],
    ];
@endphp

<div class="mt-2 flex flex-wrap gap-2">
    @foreach ($cards as $key => $card)
        <div class="flex flex-1 rounded-lg bg-white shadow"> <!-- ← added bg-white -->
            <!-- Colored left bar -->
            <div class="{{ $baseColors[$key] }} w-2 rounded-l-lg bg-linear-to-b"></div>

            <!-- Card content -->
            <div class="text-black flex flex-1 items-center justify-between p-4 font-medium">
                <div class="text-left">
                    <p class="text-sm font-semibold">{{ $card['label'] }}</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-wider">{{ $card['value'] }}</h2>
                </div>
                <div class="shrink-0 text-4xl">
                    <i class="bi {{ $card['icon'] }}" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>
