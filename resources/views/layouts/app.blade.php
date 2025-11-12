<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DEPARTMENT OF HUMAN SETTLEMENTS AND URBAN DEVELOPMENT') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="relative min-h-screen">
        <!-- Background image layer (fixed) -->
        <div class="absolute inset-0 bg-cover bg-fixed bg-center opacity-15"
            style="background-image: url('{{ asset('images/background.png') }}');">
        </div>

        <!-- Page content -->
        <div class="relative z-10">
            @include('layouts.navigation')

            <!-- Main content wrapper (adds space beside and below navbar) -->
            <div class="mt-14 sm:ml-64">
               @isset($header)
<header class="mb-4 rounded-lg bg-gray-200 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
        <!-- Header content -->
        {{ $header }}

        <!-- Live date & time aligned vertically -->
        <div id="realtime-clock" class="text-sm text-gray-700 text-right"></div>
    </div>
</header>
@endisset
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            p.type = p.type === 'password' ? 'text' : 'password';
        }
        (function clock() {
            const el = document.getElementById('realtime-clock');

            function upd() {
                const n = new Date();
                el.textContent = n.toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit'
                }) + ' | ' + n.toLocaleDateString([], {
                    weekday: 'short',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            }
            upd();
            setInterval(upd, 1000);
        })();
    </script>
</body>

</html>
