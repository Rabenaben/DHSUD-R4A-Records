<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Requests History') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Requests History')" />

        <div class="space-y-4">
            <!-- Search and Filter Bar -->
            @include('request-history.partials.search-filter-bar')

            <!-- Request History Table -->
            <div class="rounded-lg bg-white shadow-sm">
                @if($clientRequests->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200" id="request-history-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docket No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project/HOA Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clientRequests as $request)
                                <tr data-project-name="{{ strtolower($request->project_name) }}" data-docket-no="{{ strtolower($request->docket_no) }}" data-client-name="{{ strtolower($request->requested_by) }}" data-type="{{ $request->type }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $request->docket_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $request->project_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $request->requested_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $request->type === 'HOA' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $request->type }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center">
                        <p class="text-gray-500">No request history records found.</p>
                    </div>
                @endif

                <!-- No Results Message (hidden by default) -->
                <div id="no-results-message" class="hidden p-6 text-center">
                    <p class="text-gray-500">No matching records found.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Client Request Form Modal -->
    @include('request-history.partials.client-request-modal')
</x-app-layout>
