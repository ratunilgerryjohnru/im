<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WELLMEADOWS Hospital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
        <div class="relative">
            <!-- Hero Section -->
            <div class="relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
                    <div class="mb-6">
                        <div class="bg-white p-3 rounded-2xl shadow-lg inline-flex items-center justify-center w-20 h-20">
                            <svg class="w-12 h-12" style="color: #83D475;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black text-gray-900 tracking-tight">
                        WELLMEADOWS
                        <span class="block text-2xl md:text-3xl font-light text-gray-500 mt-2">Hospital</span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
                        Providing compassionate, high-quality healthcare services with state-of-the-art facilities and dedicated medical professionals.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}" class="px-8 py-3 rounded-xl font-bold text-white transition-all hover:opacity-90 shadow-lg" style="background-color: #83D475;">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-8 py-3 rounded-xl font-bold text-gray-700 bg-white border-2 border-gray-200 hover:border-[#83D475] transition-all">
                            Register New Account
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl shadow-md p-6 text-center hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800">Modern Facilities</h3>
                        <p class="text-gray-500 mt-2">State-of-the-art medical equipment and comfortable patient rooms.</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 text-center hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800">Expert Doctors</h3>
                        <p class="text-gray-500 mt-2">Highly qualified medical professionals across all specialties.</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 text-center hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800">24/7 Emergency</h3>
                        <p class="text-gray-500 mt-2">Round-the-clock emergency services for urgent medical needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>