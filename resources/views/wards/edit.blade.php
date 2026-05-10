<x-app-layout>
    <div class="min-h-screen bg-[#f0f4f8] py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-black text-gray-900">Edit Ward</h2>
                        <a href="{{ route('wards.management') }}" class="text-gray-500 hover:text-gray-700">← Back to Wards</a>
                    </div>

                    <form method="POST" action="{{ route('wards.update', $ward->ward_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Ward Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ward Name *</label>
                                <input type="text" name="ward_name" value="{{ old('ward_name', $ward->ward_name) }}" required
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3">
                                @error('ward_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ward Type -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ward Type *</label>
                                <select name="ward_type" required
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3">
                                    <option value="General" {{ $ward->ward_type == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="ICU" {{ $ward->ward_type == 'ICU' ? 'selected' : '' }}>ICU</option>
                                    <option value="Pediatric" {{ $ward->ward_type == 'Pediatric' ? 'selected' : '' }}>Pediatric</option>
                                    <option value="Maternity" {{ $ward->ward_type == 'Maternity' ? 'selected' : '' }}>Maternity</option>
                                    <option value="Emergency" {{ $ward->ward_type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                    <option value="Surgery" {{ $ward->ward_type == 'Surgery' ? 'selected' : '' }}>Surgery</option>
                                </select>
                                @error('ward_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Capacity -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Total Capacity *</label>
                                <input type="number" name="total_beds" value="{{ old('total_beds', $ward->total_beds) }}" required min="0"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3">
                                @error('total_beds')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Floor -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Floor</label>
                                <input type="text" name="floor" value="{{ old('floor', $ward->floor ?? '1') }}"
                                       class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 p-3"
                                       placeholder="e.g., 1, 2, 3, Ground">
                                @error('floor')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('wards.management') }}" 
                                   class="px-6 py-2 rounded-xl font-bold text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        style="background-color: #83D475; color: white;"
                                        class="px-6 py-2 rounded-xl font-bold text-sm hover:opacity-90 transition shadow-md">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>