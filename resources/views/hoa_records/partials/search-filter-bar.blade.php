<!-- Search and Filter Bar -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <!-- Search bar -->
    <div class="flex flex-1 items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
        <input
            class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
            id="searchInput" type="text"
            placeholder="Search by Docket No, HOA Name, Location, Province, or Municipality...">
    </div>

    <!-- Status Filter -->
    <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="statusFilter">
        <option value="">All Status</option>
        <option value="ON-SHELF">ON-SHELF</option>
        <option value="BORROWED">BORROWED</option>
        <option value="UNAVAILABLE">UNAVAILABLE</option>
    </select>

    <!-- Province Filter -->
    <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="provinceFilter">
        <option value="">All Province</option>
        @foreach($provinces as $province)
            <option value="{{ $province->province_name }}">{{ $province->province_name }}</option>
        @endforeach
    </select>

    <!-- Municipality Filter -->
    <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="municipalityFilter">
        <option value="">All Municipality</option>
        @foreach($municipalities as $municipality)
            <option value="{{ $municipality->municipality_name }}" data-province="{{ $municipality->province->province_name }}">{{ $municipality->municipality_name }}</option>
        @endforeach
    </select>
</div>
