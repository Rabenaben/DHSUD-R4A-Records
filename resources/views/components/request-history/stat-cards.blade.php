@props(['docStats' => []])

<!-- Key Stats Section -->
<div>
    <h3 class="mb-3 text-lg font-semibold text-gray-800">Document Requests Summary</h3>
    <div id="stat-cards-container" class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">
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
