<x-app-layout>
    <div class="min-h-screen bg-[#1a202c] flex flex-col items-center p-8">
        
        <div class="flex flex-col items-center mb-10">
            <div class="bg-white p-2 rounded-lg mb-2">
                 <svg class="w-10 h-10 text-[#83D475]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <h1 class="text-white text-xl font-bold tracking-tight text-center leading-tight">
                WELLMEADOWS<br><span class="font-light tracking-widest text-sm uppercase">Hospital</span>
            </h1>
        </div>

        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl p-10">
            <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-5">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Welcome Back, {{ Auth::user()->name }}!</h2>
                    <p class="text-gray-500">You are successfully logged into the management system.</p>
                </div>
                <div class="bg-[#83D475] bg-opacity-10 px-4 py-2 rounded-full">
                    <span class="text-[#83D475] font-semibold text-sm">System Active</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#83D475] transition-all cursor-pointer">
                    <h3 class="font-bold text-gray-700 mb-2">Patients</h3>
                    <p class="text-sm text-gray-500">Access patient files and records.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#83D475] transition-all cursor-pointer">
                    <h3 class="font-bold text-gray-700 mb-2">Staff</h3>
                    <p class="text-sm text-gray-500">Manage hospital staff schedules.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-xl border border-gray-100 hover:border-[#83D475] transition-all cursor-pointer">
                    <h3 class="font-bold text-gray-700 mb-2">Inventory</h3>
                    <p class="text-sm text-gray-500">Monitor medical supply levels.</p>
                </div>
            </div>
        </div>

        <div class="flex space-x-4 mt-10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-10 py-2 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-black transition-all uppercase text-sm">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-app-layout>