<nav class="fixed top-0 z-50 w-full border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="flex items-center justify-between px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-start">
            <!-- Sidebar toggle (mobile) -->
            <button
                class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden dark:text-gray-400 dark:hover:bg-gray-700"
                data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                type="button">
                <svg class="h-6 w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                    </path>
                </svg>
            </button>

            <!-- App Logo -->
            <a class="ms-2 flex" href="{{ route('dashboard') }}">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-white"
                    variant="bp" />
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-white"
                    variant="dhsud" />
                <span class="ms-2 self-center whitespace-nowrap text-xl font-semibold sm:text-2xl dark:text-white">
                    {{ config('app.name', 'DHSUDRECORDS') }}
                </span>
            </a>
        </div>

        <!-- User Dropdown -->
        <div class="hidden sm:flex sm:items-center">
            <!-- User Info -->
            <div class="mr-3 text-sm text-gray-700 dark:text-gray-300">
                {{ auth()->user()->name }} | {{ auth()->user()->remarks ?? 'N/A' }}
            </div>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="flex items-center rounded-full bg-gray-800 text-sm focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full" src="{{ asset('images/default-profile.png') }}"
                            alt="user photo">
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside
    class="fixed left-0 top-0 z-40 h-screen w-64 -translate-x-full border-r border-gray-200 bg-white pt-20 transition-transform sm:translate-x-0 dark:border-gray-700 dark:bg-gray-800"
    id="logo-sidebar" aria-label="Sidebar">
    <div class="h-full overflow-y-auto bg-white px-3 pb-4 dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <li>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i
                        class="bi bi-speedometer2 h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">Dashboard</span>
                </x-nav-link>
            </li>

            <li>
                <x-nav-link :href="route('rem_records')" :active="request()->routeIs('rem_records')">
                    <i class="bi bi-collection h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">REM Records</span>
                </x-nav-link>
            </li>

            <li>
                <x-nav-link :href="route('hoa_records')" :active="request()->routeIs('hoa_records')">
                    <i class="bi bi-houses h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">HOA Records</span>
                </x-nav-link>
            </li>

            <li>
                <x-nav-link :href="route('borrowers')" :active="request()->routeIs('borrowers')">
                    <i class="bi bi-person-lines-fill h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">Borrowers</span>
                </x-nav-link>
            </li>

            <li>
                <x-nav-link :href="route('archive')" :active="request()->routeIs('archive')">
                    <i class="bi bi-archive h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">Archived Files</span>
                </x-nav-link>
            </li>

            @if(auth()->user()->role === 'Admin')
            <li>
                <x-nav-link :href="route('accounts')" :active="request()->routeIs('accounts')">
                    <i class="bi bi-people h-5 w-5 text-gray-500 group-hover:text-gray-900 dark:text-gray-400"></i>
                    <span class="ms-3">Accounts</span>
                </x-nav-link>
            </li>
            @endif
        </ul>
    </div>
</aside>
