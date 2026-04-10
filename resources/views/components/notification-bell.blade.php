<div class="relative z-50 mr-2 flex items-center" x-data="notificationBell()">
    <x-dropdown align="right" width="96"
        contentClasses="p-4 max-h-96 overflow-y-auto w-80 sm:w-96 z-50 x-cloak bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 rounded-lg shadow-xl">
        <x-slot name="trigger">
            <button
                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                x-model="isOpen" x-on:click="if (count > 0) markAsRead()" :class="count > 0 ? 'ring-2 ring-red-500 animate-pulse' : ''" title="Notifications">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                <span
                    class="absolute -mr-3 -mt-3 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
                    x-show="count > 0" :class="count > 9 ? 'text-xs' : 'text-sm'" x-text="count > 99 ? '99+' : count">
                </span>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="mb-4 flex items-center justify-between border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overdue Notices</h3>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400"
                    x-text="totalCount > 0 ? totalCount + ' borrower' + (totalCount > 1 ? 's' : '') : 'No'"></span>
                <button 
                    @click="refreshNotices()"
                    :disabled="loading"
                    class="ml-2 p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50"
                    title="Refresh notifications">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
            <template x-if="notices.length === 0">
                <p class="py-8 text-center text-gray-500 dark:text-gray-400">No overdue notices</p>
            </template>
            <template x-for="notice in notices" :key="notice.borrower_name">
                <div class="mb-4 cursor-pointer rounded-lg border p-4 last:mb-0 hover:bg-gray-50 dark:hover:bg-gray-700"
                    @click="handleNoticeClick(notice); $dispatch('close')" title="Click to view borrower records">
                    <div class="flex items-start space-x-3">
                        <div
                            class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-orange-100 text-orange-600 ring-8 ring-white dark:bg-orange-900/20 dark:text-orange-400 dark:ring-gray-700">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z">
                                </path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white"
                                x-text="'Notice: ' + notice.borrower_name + ' needs to return Docket No. ' + notice.dockets.join(', ')">
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                x-text="notice.count + ' docket(s)'">
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </x-slot>
    </x-dropdown>
</div>
