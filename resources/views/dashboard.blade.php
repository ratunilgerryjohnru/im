<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Hospital Name Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-gray-900">WELMEADOWS HOSPITAL</h1>
                    <p class="text-gray-500 mt-1">Dashboard Overview</p>
                </div>
                
                <!-- Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Sales -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">TOTAL SALES</p>
                        <p class="text-3xl font-black text-gray-800 mt-2">$ 154,430</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="text-green-600 text-sm font-bold">▲ 13% Week ratio</span>
                            <span class="text-red-600 text-sm font-bold">▼ 10% Day ratio</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Day Sales: $15,443</p>
                    </div>
                    
                    <!-- Visits -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">VISITS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2">6,480</p>
                        <p class="text-sm text-gray-400 mt-3">Total visits this month</p>
                    </div>
                    
                    <!-- Payments -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">PAYMENTS</p>
                        <p class="text-3xl font-black text-gray-800 mt-2">5,320</p>
                        <p class="text-sm text-gray-400 mt-3">Successful transactions</p>
                    </div>
                    
                    <!-- Conversion Rate -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">CONVERSION RATE</p>
                        <p class="text-3xl font-black text-gray-800 mt-2">88%</p>
                        <p class="text-sm text-gray-400 mt-3">+5% from last month</p>
                    </div>
                </div>
                
                <!-- Charts and Rankings Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Store Sales Trend Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-black text-gray-800">Store Sales Trend</h3>
                            <div class="flex gap-2">
                                <button class="px-3 py-1 text-sm bg-blue-500 text-white rounded-lg">All week</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 rounded-lg">All month</button>
                                <button class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 rounded-lg">All year</button>
                            </div>
                        </div>
                        <div class="h-64 flex items-end justify-between gap-1">
                            <div class="w-full bg-blue-500 h-[60px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[55px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[45px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[50px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[35px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[40px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[30px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[55px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[60px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[45px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[50px] rounded-t-lg"></div>
                            <div class="w-full bg-blue-500 h-[40px] rounded-t-lg"></div>
                        </div>
                        <div class="flex justify-between mt-4 text-sm text-gray-500">
                            <span>2017</span><span>2018</span><span>2019</span><span>2020</span>
                            <span>2021</span><span>2022</span><span>2023</span><span>2024</span>
                            <span>2025</span><span>2026</span><span>2027</span><span>2028</span>
                        </div>
                    </div>
                    
                    <!-- Sales Ranking -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-black text-gray-800 mb-4">Sales ranking</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">1.</span> <span class="ml-2">No. 0 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">2.</span> <span class="ml-2">No. 1 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">3.</span> <span class="ml-2">No. 2 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">4.</span> <span class="ml-2">No. 3 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">5.</span> <span class="ml-2">No. 4 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">6.</span> <span class="ml-2">No. 5 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div><span class="font-bold text-gray-700">7.</span> <span class="ml-2">No. 6 Shop</span></div>
                                <span class="font-black text-gray-800">432,641</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>