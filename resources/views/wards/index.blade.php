<x-app-layout>
    <div class="min-h-screen bg-[#1a202c] p-8" x-data="{ 
        showForm: false, 
        wards: [],
        loading: true,
        
        async loadWards() {
            this.loading = true;
            try {
                const response = await fetch(`${window.SUPABASE_URL}/rest/v1/ward?select=*,beds:bed(bed_id,is_available)`, {
                    headers: {
                        'apikey': window.SUPABASE_KEY,
                        'Authorization': `Bearer ${window.SUPABASE_KEY}`
                    }
                });
                const data = await response.json();
                
                // Calculate stats for each ward
                this.wards = data.map(ward => {
                    const beds = ward.beds || [];
                    const total = beds.length;
                    const occupied = beds.filter(b => !b.is_available).length;
                    const available = beds.filter(b => b.is_available).length;
                    const occupancy = total > 0 ? (occupied / total) * 100 : 0;
                    const isCritical = occupancy >= 90 || (total > 0 && available <= 2);
                    
                    return {
                        ...ward,
                        total_beds: total,
                        occupied_beds: occupied,
                        available_beds: available,
                        occupancy: occupancy,
                        is_critical: isCritical
                    };
                });
            } catch (error) {
                console.error('Error loading wards:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async createWard(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/wards', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ward_name: formData.get('ward_name'),
                        ward_type: formData.get('ward_type'),
                        total_beds: parseInt(formData.get('total_beds')),
                        floor: '1'
                    })
                });
                
                if (response.ok) {
                    alert('Ward created successfully!');
                    this.showForm = false;
                    form.reset();
                    this.loadWards();
                } else {
                    alert('Error creating ward');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }" x-init="loadWards()">
        <div class="max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold text-white tracking-tight">Ward & Bed Management</h2>
                <button @click="showForm = !showForm"
                    class="bg-[#83D475] text-white px-6 py-2 rounded-xl font-bold hover:bg-opacity-90 transition shadow-lg flex items-center gap-2">
                    <span x-text="showForm ? '✕ Close' : '+ Add New Ward'"></span>
                </button>
            </div>

            <!-- Add New Ward Form -->
            <div x-show="showForm" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="mb-10 bg-white p-10 rounded-[2.5rem] shadow-2xl border-l-[12px] border-[#83D475]" x-cloak>
                
                <form @submit="createWard">
                    @csrf
                    <div class="flex flex-col md:flex-row items-end gap-8">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-3 ml-1">Ward Name</label>
                            <input type="text" name="ward_name" placeholder="Enter ward name..." required
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 focus:ring-2 focus:ring-[#83D475] focus:border-transparent text-gray-600 font-medium">
                        </div>

                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-3 ml-1">Ward Type</label>
                            <select name="ward_type" required 
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 focus:ring-2 focus:ring-[#83D475] text-gray-600 font-medium appearance-none">
                                <option value="General">General</option>
                                <option value="ICU">ICU</option>
                                <option value="Pediatric">Pediatric</option>
                            </select>
                        </div>

                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-3 ml-1">Total Capacity</label>
                            <input type="number" name="total_beds" min="1" placeholder="0" required
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 focus:ring-2 focus:ring-[#83D475] text-gray-600 font-medium">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" 
                            class="bg-[#1a202c] text-white px-12 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg active:scale-95">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="text-center py-20">
                <p class="text-white text-lg">Loading wards...</p>
            </div>

            <!-- Ward Grid -->
            <div x-show="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="ward in wards" :key="ward.ward_id">
                    <div class="bg-white rounded-[2rem] shadow-xl p-8 transition-transform hover:scale-[1.02] relative overflow-hidden">
                        
                        <div class="mb-8">
                            <h3 class="text-3xl font-black text-gray-800 leading-tight" x-text="ward.ward_name"></h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1" x-text="'FLOOR ' + (ward.floor || '1') + ' • ' + (ward.ward_type || 'General')"></p>
                        </div>

                        <div class="space-y-6 mb-8">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-blue-50 rounded-xl text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1-1 0 001 1h3m10-11l2 2m-2-2v10a1-1 0 01-1 1h-3m-6 0a1-1 0 001-1v-4a1-1 0 011-1h2a1-1 0 011 1v4a1-1 0 001 1m-6 0h6"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Total Beds</p>
                                    <p class="text-xl font-black text-gray-800" x-text="ward.total_beds"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-gray-50 rounded-xl text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Occupied</p>
                                    <p class="text-xl font-black text-gray-800" x-text="ward.occupied_beds"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-gray-50 rounded-xl text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Available</p>
                                    <p class="text-xl font-black text-gray-800" x-text="ward.available_beds"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-gray-50 rounded-xl text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Critical</p>
                                    <p class="text-xl font-black" :class="ward.is_critical ? 'text-red-600' : 'text-gray-800'" x-text="ward.is_critical ? 'High' : 'Low'"></p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-8">
                            <div class="h-2.5 rounded-full transition-all duration-700" :class="ward.is_critical ? 'bg-red-500' : 'bg-[#83D475]'" :style="'width: ' + ward.occupancy + '%'"></div>
                        </div>

                        <div class="flex gap-4">
                            <a :href="'/wards/' + ward.ward_id"
                                class="flex-1 bg-gray-50 text-gray-500 py-4 rounded-2xl font-bold text-xs hover:bg-[#1a202c] hover:text-white transition text-center border border-gray-100">
                                View Beds
                            </a>
                            <button class="flex-1 border border-gray-100 text-gray-400 py-4 rounded-2xl font-bold text-xs hover:bg-gray-50 transition">
                                Settings
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="wards.length === 0" class="col-span-full text-center py-20">
                    <p class="text-gray-500 text-lg">No wards registered in the system yet.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>