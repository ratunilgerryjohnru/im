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
            <h2 class="text-2xl font-bold text-gray-800 mb-8">Sign In</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <input type="email" id="email" name="email" placeholder="Email Address" 
                            :value="old('email')" required autofocus 
                            class="w-full px-4 py-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] focus:border-transparent outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Password" required 
                            class="w-full px-4 py-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#83D475] focus:border-transparent outline-none bg-gray-50">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 mb-8">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#83D475] focus:ring-[#83D475]">
                        <span class="ms-2">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-[#83D475] hover:underline">Forgot Password?</a>
                </div>

                <button type="submit" class="w-full bg-[#83D475] text-white font-bold py-4 rounded-xl hover:bg-opacity-90 transition-all shadow-lg">
                    Log In
                </button>
            </form>
        </div>

        <div class="flex space-x-4 mt-10">
            <a href="{{ route('login') }}" class="px-10 py-2 bg-white text-black font-bold rounded-full transition">LOGIN</a>
            <a href="{{ route('register') }}" class="px-10 py-2 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-black transition">REGISTER</a>
        </div>
    </div>
</x-guest-layout>