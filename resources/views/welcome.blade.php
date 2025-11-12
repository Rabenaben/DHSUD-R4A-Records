<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>DHSUD | Login</title>

    <!-- Tailwind via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-gray-50">

    <!-- Header -->
    <header
<<<<<<< HEAD
        class="fixed inset-x-0 top-0 z-50 flex items-center justify-between bg-linear-to-b from-gray-200 to-white px-4 py-3 backdrop-blur-sm">
=======
        class="fixed inset-x-0 top-0 z-50 flex items-center justify-between bg-gradient-to-b from-gray-200 to-white px-4 py-3 backdrop-blur-sm">
>>>>>>> 5386df9aa3e25fabb4f020afd61feaf6befc6ea3
        <div class="flex items-center gap-3">
            <img class="h-10" src="{{ asset('images/logo2.png') }}" alt="logo">
            <h1 class="bg-linear-to-r from-blue-600 to-red-600 bg-clip-text text-xl font-bold text-transparent">
                DEPARTMENT OF HUMAN SETTLEMENTS AND URBAN DEVELOPMENT
            </h1>
        </div>
        <div class="text-sm text-gray-600" id="realtime-clock"></div>
    </header>

    <!-- Main -->
    <main class="flex h-full pt-16">
        <!-- Left image -->
        <div class="hidden flex-1 bg-cover bg-center md:block"
            style="background-image:url('{{ asset('images/dhsud.jpeg') }}')"></div>

        <!-- Login Section -->
        <div class="flex w-full items-center justify-center p-6 md:w-96">
            <div class="w-full max-w-md rounded-2xl bg-white/20 backdrop-blur-md p-6">
                <img class="mx-auto mb-4 w-28" src="{{ asset('images/logo1.png') }}" alt="logo">
                <h2 class="text-center text-2xl font-bold text-blue-800">DHSUD</h2>
                <p class="mb-4 text-center text-sm text-gray-500">Region IV-A</p>

                <!-- Laravel Login Form -->
                <form class="space-y-3" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username -->
                    <div>
                        <x-input-label for="username" :value="__('Username')" />
                        <x-text-input class="mt-1 block w-full" id="username" type="text" name="username"
                            :value="old('username')" required autofocus autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('username')" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input class="mt-1 block w-full" id="password" type="password" name="password" required
                            autocomplete="current-password" />

                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-4 block">
                        <label class="inline-flex items-center" for="remember_me">
                            <input class="shadow-xs rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                id="remember_me" type="checkbox" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-4">
                        @if (Route::has('password.request'))
                            <a class="focus:outline-hidden rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        @auth
                            <a class="rounded-xs inline-block border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                href="{{ url('/dashboard') }}" ->
                                Dashboard
                            </a>
                        @else
                            <x-primary-button class="ms-3">
                                {{ __('Log in') }}
                            </x-primary-button>
                        @endauth
                    </div>
                </form>

                <p class="mt-4 text-center text-xs text-gray-500">By using this service you agree to the DHSUD Terms of
                    Use and Privacy Statement.</p>
            </div>
        </div>
    </main>

    <!-- Scripts -->
<<<<<<< HEAD
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
=======
    <<script>
        document.addEventListener("DOMContentLoaded", function() {
            function togglePassword() {
                const p = document.getElementById('password');
                p.type = p.type === 'password' ? 'text' : 'password';
>>>>>>> 5386df9aa3e25fabb4f020afd61feaf6befc6ea3
            }

            function clock() {
                const el = document.getElementById('realtime-clock');
                if (!el) return;

                function upd() {
                    const n = new Date();
                    el.textContent = n.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                        second: '2-digit' // show seconds
                    }) + ' | ' + n.toLocaleDateString([], {
                        weekday: 'short',
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                }

                upd();
                setInterval(upd, 1000);
            }

            clock(); // <- Call the function here
        });
    </script>
</body>

</html>
