<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="mb-6 text-center">
            <div class="bg-white p-3 rounded-2xl shadow-md inline-flex items-center justify-center w-16 h-16">
                <svg class="w-10 h-10" style="color: #83D475;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mt-3">WELLMEADOWS HOSPITAL</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to your account</p>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-2xl border border-gray-200">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" class="font-bold text-gray-700" />
                    <x-text-input id="email" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="font-bold text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#83D475] focus:ring-[#83D475]" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-[#83D475] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#83D475]" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3 px-6 py-3 rounded-xl font-bold" style="background-color: #83D475;">
                        {{ __('Login') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-[#83D475] hover:underline">Register</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>