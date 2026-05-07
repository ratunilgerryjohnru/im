<x-app-layout>
    <div class="min-h-screen bg-[#1a202c] p-8" x-data="{ showForm: false }">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold text-white tracking-tight">Ward & Bed Management</h2>
                <button 
                    @click="showForm = !showForm" 
                    class="bg-[#83D475] text-white px-6 py-2 rounded-xl font-bold hover:bg-opacity-90 transition shadow-lg flex items-center gap-2"
                >
                    <span x-text="showForm ? '✕ Close' : '+ Add New Ward'"></span>
                </button>
            </div>

            <div 
                x-show="showForm" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="mb-10 bg-white p-8 rounded-2xl shadow-2xl border-l-8 border-[#83D475]"
                style="display: none;"
            >
                <h3 class="text-xl font-bold text-gray-800 mb-6 italic">Register New Hospital Ward</h3>
                <form action="{{ route('wards.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Ward Name</label>
                            <input type="text" name="ward_name" placeholder="e.g. Ward 101" required 
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-[#83D475] focus:border-transparent outline-none bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Category</label>
                            <select name="category" required class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-[#83D475] bg-gray-50 px-4 py-3">
                                <option value="General">General</option>
                                <option value="ICU">ICU</option>
                                <option value="Pediatric">Pediatric</option>
                                <option value="Surgical">Surgical</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Total Beds</label>
                            <input type="number" name="total_beds" min="1" placeholder="Total Capacity" required 
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-[#83D475] bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Available Beds</label>
                            <input type="number" name="available_beds" min="0" placeholder="Currently Free" required 
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-[#83D475] bg-gray-50 px-4 py-3">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-[#1a202c] text-white px-10 py-3 rounded-xl font-bold hover:bg-opacity-90 transition shadow-lg">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($wards as $ward)
                    <div class="bg-white rounded-2xl shadow-xl p-6 border-l-8 border-[#83D475] transform hover:scale-[1.02] transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $ward->ward_name }}</h3>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $ward->category }}</span>
                            </div>
                            <div class="bg-green-50 text-[#2d5a27] text-xs font-bold px-3 py-1 rounded-full border border-green-100">
                                {{ $ward->available_beds }} / {{ $ward->total_beds }} Beds
                            </div>
                        </div>

                        @php
                            $percent = ($ward->total_beds > 0) ? ($ward->available_beds / $ward->total_beds) * 100 : 0;
                        @endphp
                        
                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-6">
                            <div class="bg-[#83D475] h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('wards.show', $ward->id) }}" class="flex-1 bg-gray-50 text-gray-600 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-100 transition text-center">
                                View Details
                            </a>
                            <button class="flex-1 border border-gray-100 text-gray-400 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-50 transition">Edit</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-gray-500 text-lg">No wards found in the WELLMEADOWS database.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>