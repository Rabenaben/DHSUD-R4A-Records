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
            <h3 class="mb-3 text-lg font-semibold text-gray-800">Document Requests Summary</h3>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-7">
                @foreach($docStats as $docName => $count)
                    @php
                        $colors = [
                            'Certificate of Incorporation' => 'from-blue-500 to-blue-700',
                            'Certificate of Amended By-Laws' => 'from-indigo-500 to-indigo-700',
                            'Certificate of Amended Articles of Incorporation' => 'from-purple-500 to-purple-700',
                            'Articles of Incorporation' => 'from-pink-500 to-pink-700',
                            'By-Laws' => 'from-rose-500 to-rose-700',
                            'Annual Report' => 'from-amber-500 to-amber-700',
                            'Election Report' => 'from-teal-500 to-teal-700',
                        ];
                        $bgClass = $colors[$docName] ?? 'from-gray-500 to-gray-700';
                    @endphp
                    <div class="relative flex h-20 items-center justify-between rounded-lg bg-white p-3 shadow transition-transform duration-200 hover:-translate-y-2 hover:transform">
                        <!-- LEFT COLORED BAR -->
                        <div class="bg-linear-to-r {{ $bgClass }} absolute bottom-0 left-0 top-0 w-2 rounded-l-lg"></div>
                        <!-- Text Content -->
                        <div class="flex flex-col pl-2">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">
                                {{ $count }}
                            </h2>
                            <p class="mt-1 text-xs font-semibold md:text-sm line-clamp-2" title="{{ $docName }}">
                                {{ $docName }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-4 mt-6">
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
