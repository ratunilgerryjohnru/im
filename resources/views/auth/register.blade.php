<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="mb-6 text-center">
            <div class="bg-white p-3 rounded-2xl shadow-md inline-flex items-center justify-center w-16 h-16">
                <svg class="w-10 h-10" style="color: #83D475;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mt-3">WELLMEADOWS HOSPITAL</h1>
            <p class="text-gray-500 text-sm mt-1">Create your account</p>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-2xl border border-gray-200">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" class="font-bold text-gray-700" />
                    <x-text-input id="name" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email Address')" class="font-bold text-gray-700" />
                    <x-text-input id="email" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="font-bold text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-bold text-gray-700" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-[#83D475] focus:ring-[#83D475]" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button class="ms-4 px-6 py-3 rounded-xl font-bold" style="background-color: #83D475;">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-bold text-[#83D475] hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>