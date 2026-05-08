<x-app-layout>
    <div class="min-h-screen bg-[#1a202c] p-8" x-data="{ showForm: false }">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold text-white tracking-tight">Ward & Bed Management</h2>
                <button @click="showForm = !showForm" class="bg-[#83D475] text-white px-6 py-2 rounded-xl font-bold hover:bg-opacity-90 transition shadow-lg">
                    <span x-text="showForm ? '✕ Close' : '+ Add New Ward'"></span>
                </button>
            </div>

            <div x-show="showForm" x-transition class="mb-10 bg-white p-8 rounded-2xl shadow-2xl border-l-8 border-[#83D475]" style="display: none;">
                <form action="{{ route('wards.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Ward Name</label>
                            <input type="text" name="ward_name" required class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Ward Type (Category)</label>
                            <select name="ward_type" required class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3">
                                <option value="General">General</option>
                                <option value="ICU">ICU</option>
                                <option value="Pediatric">Pediatric</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Location</label>
                            <input type="text" name="location" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Total Beds</label>
                            <input type="number" name="total_beds" min="1" required class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Available Beds</label>
                            <input type="number" name="available_beds" min="0" required class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-[#1a202c] text-white px-10 py-3 rounded-xl font-bold">Confirm Registration</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($wards as $ward)
                    <div class="bg-white rounded-2xl shadow-xl p-6 border-l-8 border-[#83D475]">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $ward->ward_name }}</h3>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $ward->ward_type }}</span>
                            </div>
                            <div class="bg-green-50 text-[#2d5a27] text-xs font-bold px-3 py-1 rounded-full">
                                {{ $ward->available_beds }} / {{ $ward->total_beds }} Beds
                            </div>
                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-6">
                            <div class="bg-[#83D475] h-2.5 rounded-full" style="width: {{ $ward->occupancy_rate }}%"></div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('wards.show', $ward->ward_id) }}" 
                               class="flex-1 bg-gray-50 text-gray-600 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-100 transition text-center">
                               View Details
                            </a>
                            <button class="flex-1 border border-gray-100 text-gray-400 py-2.5 rounded-xl font-bold text-sm">Edit</button>
                        </div>
                    </div>
                @empty
                    <p class="text-white">No wards found.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>