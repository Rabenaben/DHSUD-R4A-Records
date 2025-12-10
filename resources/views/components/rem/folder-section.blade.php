<div class="rounded-xl border border-gray-300 bg-amber-50 p-2 shadow">
    <div class="grid grid-cols-2 justify-items-center gap-4 sm:grid-cols-3 md:grid-cols-6">
        @foreach ($provinces as $province)
            <div class="folder flex cursor-pointer flex-col items-center space-y-2 transition-transform duration-300 hover:scale-110"
                data-province="{{ is_object($province) ? $province->province_id : $province }}"
                data-province-name="{{ is_object($province) ? $province->province_name : $province }}">
                <svg class="h-16 w-16 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M3 6a1 1 0 011-1h6.586A2 2 0 0112 5.586L13.414 7H20a1 1 0 011 1v11a1 1 0 01-1 1H4a1 1 0 01-1-1V6z" />
                </svg>
                <span class="text-center text-sm font-semibold uppercase text-gray-800">
                    {{ is_object($province) ? $province->province_name : $province }}
                </span>
            </div>
        @endforeach
    </div>
</div>
