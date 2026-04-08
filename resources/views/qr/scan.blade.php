<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('QR Scanner') }}
        </h2>
    </x-slot>

    <div class="relative min-h-[calc(100vh-6rem)] px-4 py-6">
        <div class="sm:max-w-md absolute top-1/2 left-1/2 w-[95%] transform -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-lg bg-white shadow-xl transition-all border border-gray-200 p-4">
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Point the camera at a QR code for a docket. The system will open the docket information page automatically.
                </p>
            </div>

            <div class="mb-4 flex flex-col gap-2">
                <label for="scan-mode" class="text-sm font-semibold text-gray-700">Scan Mode</label>
                <select id="scan-mode"
                    class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="camera">Camera Scanner</option>
                    <option value="usb">USB Scanner</option>
                </select>
            </div>

            <div id="camera-scan-section" class="flex flex-col items-center gap-3">
                <div id="qr-reader" class="min-h-[180px] max-w-[220px] w-full rounded-lg border border-dashed border-gray-300 bg-gray-50"></div>
                <div class="w-full">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="text-sm font-semibold text-gray-700">Controls</div>
                        <div class="mt-2 flex flex-col gap-2">
                            <button id="qr-start-btn" type="button"
                                class="rounded bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Start Scanner
                            </button>
                            <button id="qr-stop-btn" type="button"
                                class="rounded bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                                Stop Scanner
                            </button>
                        </div>
                        <div class="mt-3 text-xs text-gray-500" id="qr-status">
                            Camera is idle.
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500 text-center">
                        Tip: Use a well-lit area and keep the code steady for faster detection.
                    </div>
                </div>
            </div>

            <div id="usb-scan-section" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4 hidden">
                <div class="text-sm font-semibold text-gray-700">USB Scanner</div>
                <p class="mt-1 text-xs text-gray-500">
                    If your scanner works like a keyboard, click the field below and scan the QR code.
                </p>
                <form id="usb-scan-form" class="mt-3 flex flex-col gap-2 sm:flex-row">
                    <input id="usb-scan-input" type="text" autocomplete="off"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Scan result will appear here" />
                    <button id="usb-scan-submit" type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Open Docket
                    </button>
                    <button id="usb-scan-focus" type="button"
                        class="rounded bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                        Focus Field
                    </button>
                </form>
                <div class="mt-2 text-xs text-gray-500" id="usb-status">
                    Waiting for scan input.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
