<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>

</html>
