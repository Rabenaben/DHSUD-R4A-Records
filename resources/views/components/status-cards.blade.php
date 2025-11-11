@php
    if (isset($theme) && $theme === 'rem') {
        $colors = [
            'total' => 'bg-linear-to-br from-gray-100 to-gray-400 border-gray-300 border-2 text-slate-800',
            'onShelf' => 'bg-linear-to-br from-lime-300 to-lime-600 border-gray-300 border-2 text-slate-800',
            'unavailable' => 'bg-linear-to-br from-yellow-300 to-yellow-600 border-gray-300 border-2 text-slate-800',
            'borrowed' => 'bg-linear-to-br from-red-400 to-red-700 border-gray-300 border-2 text-slate-800',
        ];
    } else {
        $colors = [
            'total' => 'bg-linear-to-br from-gray-300 to-gray-600 border-gray-300 border-2 text-zinc-100',
            'onShelf' => 'bg-linear-to-br from-green-400 to-green-700 border-gray-300 border-2 text-zinc-100',
            'unavailable' => 'bg-linear-to-br from-yellow-400 to-yellow-700 border-gray-300 border-2 text-zinc-100',
            'borrowed' => 'bg-linear-to-br from-red-500 to-red-800 border-gray-300 border-2 text-zinc-100',
        ];
    }

@endphp

<div class="flex flex-wrap gap-5 p-2">
    <div class="{{ $colors['total'] }} min-w-40 flex-1 rounded-lg p-2 text-center font-medium shadow">
        Total Dockets<br><strong class="text-xl">{{ $totalDockets }}</strong>
    </div>
    <div class="{{ $colors['onShelf'] }} min-w-40 flex-1 rounded-lg p-2 text-center font-medium shadow">
        On-Shelf<br><strong class="text-xl">{{ $onShelf }}</strong>
    </div>
    <div class="{{ $colors['unavailable'] }} min-w-40 flex-1 rounded-lg p-2 text-center font-medium shadow">
        Unavailable<br><strong class="text-xl">{{ $unavailable }}</strong>
    </div>
    <div class="{{ $colors['borrowed'] }} min-w-40 flex-1 rounded-lg p-2 text-center font-medium shadow">
        Borrowed<br><strong class="text-xl">{{ $borrowed }}</strong>
    </div>
</div>
