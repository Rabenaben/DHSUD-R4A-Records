<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? Enter your username, the admin secret code, and a new password to reset it.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.reset') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input class="mt-1 block w-full" id="username" type="text" name="username" :value="old('username')" required
                autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Secret Code -->
        <div class="mt-4">
            <x-input-label for="secret_code" :value="__('Admin Secret Code')" />
            <x-text-input class="mt-1 block w-full" id="secret_code" type="password" name="secret_code" required />
            <x-input-error class="mt-2" :messages="$errors->get('secret_code')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input class="mt-1 block w-full" id="password" type="password" name="password" required />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input class="mt-1 block w-full" id="password_confirmation" type="password"
                name="password_confirmation" required />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
