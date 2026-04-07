<!-- Search and Filter Bar for Request History -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <!-- Search bar -->
    <div class="flex items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 min-w-0 flex-1">
        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
            class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
            id="requestSearchInput" type="text"
            placeholder="Enter HOA or REM Project...">
    </div>

    <!-- Type Filter (HOA/REM) -->
    <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700 min-w-[140px]" id="typeFilter">
        <option value="">All Types</option>
        <option value="HOA">HOA</option>
        <option value="REM">REM</option>
    </select>

    <!-- Add Client Request Form Button -->
    <button class="rounded-xl border border-gray-300 bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" id="addClientRequestBtn">
        Add Client Request Form
    </button>
</div>
