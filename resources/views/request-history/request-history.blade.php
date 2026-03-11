<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Requests History') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Requests History')" />

        <!-- Key Stats Section -->
        <div class="mt-4">
            <!-- Toggle Buttons -->
            <div class="mb-4 flex justify-center space-x-4">
                <button
                    class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    id="btn-hoa">
                    HOA
                </button>
                <button
                    class="rounded-lg bg-gray-200 px-6 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    id="btn-rem">
                    REM
                </button>
            </div>

            <!-- Single Chart Container - Full Width -->
            <div class="w-full">
                <h3 class="mb-3 text-center text-lg font-semibold text-gray-800" id="chart-title">HOA Document Requests
                </h3>
                <x-request-history.doc-chart :hoaStats="$hoaStats" :remStats="$remStats" :hoaCertified="$hoaCertified" :hoaNotCertified="$hoaNotCertified" :remCertified="$remCertified" :remNotCertified="$remNotCertified" />
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <!-- Search and Filter Bar -->
            @include('request-history.partials.search-filter-bar')

            <!-- Request History Table -->
            <div class="max-h-70 overflow-x-auto rounded-lg bg-white shadow-sm" id="request-history-container">
                <table class="min-w-full table-fixed divide-y divide-gray-200" id="request-history-table"
                    style="{{ $clientRequests->count() === 0 ? 'display: none;' : '' }}">
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr>
                            @php
                                $headers = [
                                    ['width' => 'w-[15%]', 'label' => 'Date'],
                                    ['width' => 'w-[15%]', 'label' => 'Docket No.'],
                                    ['width' => 'w-[40%]', 'label' => 'Project/HOA Name'],
                                    ['width' => 'w-[15%]', 'label' => 'Requested By'],
                                    ['width' => 'w-[15%]', 'label' => 'Type'],
                                ];
                            @endphp
                            @foreach ($headers as $header)
                                <th class="{{ $header['width'] }} px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ $header['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($clientRequests as $request)
                            <tr class="cursor-pointer transition hover:bg-gray-100"
                                data-project-name="{{ strtolower($request->project_name) }}"
                                data-docket-no="{{ strtolower($request->docket_no) }}"
                                data-client-name="{{ strtolower($request->requested_by) }}"
                                data-type="{{ $request->type }}">
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ $request->docket_no }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ $request->project_name }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ $request->requested_by }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="{{ $request->type === 'HOA' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                        {{ $request->type }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- No Records Message -->
                <div class="p-6 text-center" id="no-records-message"
                    style="{{ $clientRequests->count() > 0 ? 'display: none;' : '' }}">
                    <p class="text-gray-500">No request history records found.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Request Form Modal (Add/View) -->
    <x-request-history.client-request-modal />
</x-app-layout>
