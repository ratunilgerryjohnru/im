<x-app-layout>
    <div class="min-h-screen bg-[#f0f4f8] py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-black text-gray-900">➕ Add New Ward</h2>
                        <a href="{{ route('wards.management') }}" class="text-gray-500 hover:text-gray-700">← Back to Wards</a>
                    </div>

                    <form method="POST" action="{{ route('wards.store') }}">
                        @csrf

                        <div class="space-y-6">
                            <!-- Ward Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ward Name *</label>
                                <input type="text" name="ward_name" value="{{ old('ward_name') }}" required
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3"
                                       placeholder="e.g., ICU Ward C">
                                @error('ward_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ward Type -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ward Type *</label>
                                <select name="ward_type" required
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3">
                                    <option value="" disabled selected>Select ward type</option>
                                    <option value="General">General</option>
                                    <option value="ICU">ICU</option>
                                    <option value="Pediatric">Pediatric</option>
                                    <option value="Maternity">Maternity</option>
                                    <option value="Emergency">Emergency</option>
                                    <option value="Surgery">Surgery</option>
                                </select>
                                @error('ward_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Beds -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Total Beds *</label>
                                <input type="number" name="total_beds" value="{{ old('total_beds', 10) }}" required min="1"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3">
                                <p class="text-xs text-gray-400 mt-1">Beds will be automatically created (B-001, B-002, etc.)</p>
                                @error('total_beds')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Floor -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Floor</label>
                                <input type="text" name="floor" value="{{ old('floor', '1') }}"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3"
                                       placeholder="e.g., 1, 2, 3, Ground">
                                @error('floor')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Buttons - Create Ward button with #83D475 color -->
                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('wards.management') }}" 
                                   class="px-6 py-2 rounded-xl font-bold text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        style="background-color: #83D475; color: white;"
                                        class="px-6 py-2 rounded-xl font-bold text-sm hover:opacity-90 transition shadow-md">
                                    Create Ward
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>