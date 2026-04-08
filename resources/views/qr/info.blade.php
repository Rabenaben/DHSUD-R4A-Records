<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ strtoupper($type) }} Docket Information
        </h2>
    </x-slot>

    <div class="relative min-h-[calc(100vh-6rem)] px-4 py-6">
        <div class="sm:max-w-7xl absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full border border-gray-200 p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Docket No: <span class="font-semibold text-gray-800">{{ $docketNo }}</span>
                </div>
                <a href="{{ route('qr.scan') }}"
                    class="rounded bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                    Back to Scanner
                </a>
            </div>

            @if(!$record)
                <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-sm text-red-700">
                    This docket was not found. Please verify the QR code or try again.
                </div>
            @else
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @if($type === 'hoa')
                            <div>
                                <div class="text-xs text-gray-500">HOA ID</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $record->hoa_id ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">HOA Name</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $record->hoa_name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Classification</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $record->classification ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">HOA Status</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $record->hoa_status ?? 'N/A' }}</div>
                            </div>
                        @else
                            <div>
                                <div class="text-xs text-gray-500">Project Name</div>
                                <div class="text-sm font-semibold text-gray-800">{{ $record->project_name ?? 'N/A' }}</div>
                            </div>
                        @endif

                        <div>
                            <div class="text-xs text-gray-500">Location</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->location ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Province</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->province->province_name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Municipality</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->municipality->municipality_name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Status</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->status ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Quantity</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->quantity ?? 'N/A' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-xs text-gray-500">Remarks</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $record->remarks ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
