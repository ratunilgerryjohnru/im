<x-guest-layout>
    <div class="min-h-screen bg-[#1a202c] flex flex-col items-center justify-center p-4">
        
        <div class="flex flex-col items-center mb-8">
            <div class="bg-white p-2 rounded-lg mb-2">
                 <svg class="w-12 h-12 text-[#83D475]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <h1 class="text-white text-2xl font-bold tracking-tight text-center leading-tight">
                WELLMEADOWS<br><span class="font-light tracking-widest text-lg">HOSPITAL</span>
            </h1>
        </div>

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">Create Account</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    
                    <div>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Account Number / ID" required 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('account_number')" class="mt-1" />
                    </div>

                    <div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    
                    <div>
                        <input type="password" name="password" placeholder="Password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 mb-6">
                    <a href="{{ route('login') }}" class="text-sm text-[#83D475] hover:underline">Already registered?</a>
                </div>

                <button type="submit" class="w-full bg-[#83D475] text-white font-bold py-4 rounded-xl hover:bg-opacity-90 transition shadow-lg">
                    Register
                </button>
            </form>
        </div>

        <div class="flex space-x-4 mt-10">
            <a href="{{ route('login') }}" class="px-10 py-2 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-black transition">LOGIN</a>
            <a href="{{ route('register') }}" class="px-10 py-2 bg-white text-black font-bold rounded-full transition">REGISTER</a>
        </div>
    </div>
</x-guest-layout>