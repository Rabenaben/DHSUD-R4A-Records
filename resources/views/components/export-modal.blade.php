@props(['type' => 'hoa', 'provinces' => []])

<!-- Export Modal -->
<x-modal name="export-{{ $type }}" maxWidth=".25 xl">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Export {{ strtoupper($type) }} Records</h2>
        
        <form id="export-{{ $type }}-form" class="space-y-4">
            <!-- Province Filter -->
            <div>
                <x-input-label value="Province" :required="false" />
                <select id="export-{{ $type }}-province" name="province_id"
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
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
                <select id="export-{{ $type }}-municipality" name="municipality_id"
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    disabled>
                    <option value="">All Municipalities</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                    id="cancel-export-{{ $type }}-btn"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button"
                    id="export-{{ $type }}-submit-btn"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Export to Excel
                </button>
            </div>
        </form>
    </div>
</x-modal>
