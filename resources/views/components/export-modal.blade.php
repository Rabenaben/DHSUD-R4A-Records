@props(['type' => 'hoa', 'provinces' => []])

<!-- Export Modal -->
<x-modal name="export-{{ $type }}" maxWidth=".25 xl">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Export {{ strtoupper($type) }} Records</h2>

        <form class="space-y-4" id="export-{{ $type }}-form">
            <!-- Province Filter -->
            <div>
                <x-input-label value="Province" :required="false" />
                <select
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    id="export-{{ $type }}-province" name="province_id">
                    <option value="">All Provinces</option>
                    @foreach ($provinces as $province)
                        <option value="{{ is_object($province) ? $province->province_id : $province }}">
                            {{ is_object($province) ? $province->province_name : $province }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Municipality Filter -->
            <div>
                <x-input-label value="Municipality" :required="false" />
                <select
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    id="export-{{ $type }}-municipality" name="municipality_id" disabled>
                    <option value="">All Municipalities</option>
                </select>
            </div>

            <!-- Modern Export Options Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Excel Export Card -->
                <div
                    class="bg-linear-to-br group rounded-xl border border-emerald-200 from-emerald-50 to-emerald-100 p-6 shadow-md">
                    <div class="flex flex-col items-center space-y-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 transition-transform group-hover:scale-110">
                            <svg class="h-10 w-10 text-emerald-600" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M12 10.5v6m-3-3h6M6 9h14a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10a1 1 0 0 1 1-1z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Excel</h3>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-2 hover:transform hover:bg-green-700 hover:shadow-2xl"
                            id="export-{{ $type }}-submit-btn" type="button">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M12 10.5v6m-3-3h6M3 19h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2z"></path>
                            </svg>
                            Export to Excel
                        </button>
                    </div>
                </div>

                <!-- SQL Export Card -->
                <div
                    class="bg-linear-to-br group rounded-xl border border-blue-200 from-blue-50 to-blue-100 p-6 shadow-md">
                    <div class="flex flex-col items-center space-y-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100 transition-transform group-hover:scale-110">
                            <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">SQL</h3>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-2 hover:transform hover:bg-blue-700 hover:shadow-2xl"
                            id="export-{{ $type }}-sql-btn" type="button">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M12 10.5v6m-3-3h6M3 19h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2z"></path>
                            </svg>
                            Export to SQL
                        </button>
                    </div>
                </div>

                <!-- Files Export Card -->
                <div
                    class="bg-linear-to-br group rounded-xl border border-purple-200 from-purple-50 to-purple-100 p-6 shadow-md">
                    <div class="flex flex-col items-center space-y-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 transition-transform group-hover:scale-110">
                            <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H9.375z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Files</h3>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-purple-600 px-6 py-3 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-2 hover:transform hover:bg-purple-700 hover:shadow-2xl"
                            id="export-{{ $type }}-files-btn" type="button">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                                    d="M12 10.5v6m-3-3h6M3 19h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2z"></path>
                            </svg>
                            Export to Files
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cancel Button -->
            <div class="flex justify-end">
                <button
                    class="cursor-pointer rounded-lg bg-gray-500 px-6 py-2 text-sm font-semibold text-white transition-colors hover:bg-gray-600"
                    id="cancel-export-{{ $type }}-btn" type="button">
                    Cancel
                </button>
            </div>
        </form>

        <!-- Loading Overlay -->
        <div id="export-loading-{{ $type }}" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="flex flex-col items-center space-y-4 rounded-xl bg-white p-8 shadow-2xl">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600"></div>
                <div class="text-lg font-semibold text-gray-900">Exporting Records...</div>
                <div class="text-sm text-gray-500">Please wait while we prepare your download. Do not close this tab.</div>
            </div>
        </div>
    </div>
</x-modal>
