<x-app-layout>
    <div class="min-h-screen bg-gray-100 p-8" x-data="wardManagementData()" x-init="loadWards()">
        
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">🏥 Hospital Ward & Bed Management</h1>
                <p class="text-gray-500 mt-1">Manage patient beds and ward occupancy</p>
            </div>

            <!-- Ward Overview Title -->
            <div class="mb-8">
                <h2 class="text-2xl font-black text-gray-800">🏨 Ward Overview</h2>
                <p class="text-gray-500 text-sm mt-1">Current ward statistics and occupancy</p>
            </div>

            <!-- Error State -->
            <div x-show="error" class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-red-600" x-text="error"></p>
            </div>

            <!-- Loading State -->
            <div x-show="loading" class="text-center py-20">
                <p class="text-gray-500 text-lg">Loading ward statistics...</p>
            </div>

            <div x-show="!loading && !error">
                <!-- SUMMARY STATISTICS CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Total Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2" x-text="totalBedsAll"></p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Occupied Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2" x-text="totalOccupiedAll"></p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Vacant Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2" x-text="totalAvailableAll"></p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Occupancy Rate</p>
                                <p class="text-3xl font-black text-gray-800 mt-2" x-text="overallOccupancyRate + '%'"></p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Critical Alert -->
                <div x-show="criticalWardsCount > 0" class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-500 rounded-full p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-red-800 font-bold">Critical Occupancy Alert</p>
                            <p class="text-red-600 text-sm" x-text="criticalWardsCount + ' ward(s) are at or above 90% occupancy. Please review bed allocation.'"></p>
                        </div>
                    </div>
                </div>

                <!-- Wards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="ward in wards" :key="ward.ward_id">
                        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 border-2 border-gray-300">
                            <div class="px-5 pt-5 pb-3 border-b border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-black text-gray-800" x-text="ward.ward_name"></h3>
                                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1" x-text="'Floor ' + ward.floor + ' • ' + ward.ward_type"></p>
                                    </div>
                                    <span x-show="ward.occupancy >= 90" class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full">CRITICAL</span>
                                    <span x-show="ward.occupancy >= 70 && ward.occupancy < 90" class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full">WARNING</span>
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Total Beds</p>
                                        <p class="text-2xl font-black text-gray-800" x-text="ward.total_beds"></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Occupied</p>
                                        <p class="text-2xl font-black text-gray-800" x-text="ward.occupied_beds"></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Vacant</p>
                                        <p class="text-2xl font-black text-gray-800" x-text="ward.available_beds"></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Occupancy</p>
                                        <p class="text-2xl font-black text-gray-800" x-text="Math.round(ward.occupancy) + '%'"></p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span class="font-bold">Occupancy Rate</span>
                                        <span class="font-bold text-gray-800" x-text="Math.round(ward.occupancy) + '%'"></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-500" :class="ward.occupancy >= 90 ? 'bg-red-500' : (ward.occupancy >= 70 ? 'bg-orange-500' : 'bg-green-500')" :style="'width: ' + ward.occupancy + '%'"></div>
                                    </div>
                                    <p class="text-right text-[10px] text-gray-400 mt-1" x-text="ward.available_beds + ' bed(s) vacant • ' + ward.occupied_beds + ' occupied'"></p>
                                </div>
                                
                                <div class="flex gap-2 mt-4">
                                    <a :href="'/wards/' + ward.ward_id" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-sm">View Beds</a>
                                    <a :href="'/wards/' + ward.ward_id + '/edit'" class="flex-1 border border-gray-200 text-gray-500 py-2.5 rounded-xl font-bold text-xs text-center hover:bg-gray-50 transition duration-200">Settings</a>
                                    <button @click="deleteWard(ward.ward_id)" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-sm">Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="wards.length === 0" class="col-span-full text-center py-20 bg-white rounded-2xl">
                        <p class="text-gray-500 text-lg">No wards found.</p>
                    </div>
                </div>
                
                <!-- ADD NEW WARD BUTTON -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('wards.create') }}" style="background-color: #83D475; color: white;" class="px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:opacity-90 transition shadow-md inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        ADD NEW WARD
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function wardManagementData() {
            return {
                wards: [],
                totalBedsAll: 0,
                totalOccupiedAll: 0,
                totalAvailableAll: 0,
                overallOccupancyRate: 0,
                criticalWardsCount: 0,
                loading: true,
                error: null,
                
                async loadWards() {
                    this.loading = true;
                    this.error = null;
                    
                    console.log('Supabase URL:', window.SUPABASE_URL);
                    console.log('Supabase Key exists:', !!window.SUPABASE_KEY);
                    
                    if (!window.SUPABASE_URL || !window.SUPABASE_KEY) {
                        this.error = 'Supabase configuration missing. Please check .env file.';
                        this.loading = false;
                        return;
                    }
                    
                    try {
                        const url = `${window.SUPABASE_URL}/rest/v1/ward?select=*`;
                        console.log('Fetching from:', url);
                        
                        const response = await fetch(url, {
                            headers: {
                                'apikey': window.SUPABASE_KEY,
                                'Authorization': `Bearer ${window.SUPABASE_KEY}`
                            }
                        });
                        
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        const data = await response.json();
                        console.log('Wards data:', data);
                        
                        if (!data || data.length === 0) {
                            this.error = 'No wards found in the database.';
                            this.loading = false;
                            return;
                        }
                        
                        this.wards = [];
                        for (const ward of data) {
                            const bedsResponse = await fetch(`${window.SUPABASE_URL}/rest/v1/bed?ward_id=eq.${ward.ward_id}&select=*`, {
                                headers: {
                                    'apikey': window.SUPABASE_KEY,
                                    'Authorization': `Bearer ${window.SUPABASE_KEY}`
                                }
                            });
                            const bedsData = await bedsResponse.json();
                            const beds = Array.isArray(bedsData) ? bedsData : (bedsData.data || []);
                            
                            const total = beds.length;
                            const occupied = beds.filter(b => !b.is_available).length;
                            const available = beds.filter(b => b.is_available).length;
                            const occupancy = total > 0 ? (occupied / total) * 100 : 0;
                            
                            this.wards.push({
                                ward_id: ward.ward_id,
                                ward_name: ward.ward_name,
                                floor: ward.floor || '1',
                                ward_type: ward.ward_type || 'General',
                                total_beds: total,
                                occupied_beds: occupied,
                                available_beds: available,
                                occupancy: occupancy,
                                is_critical: occupancy >= 90
                            });
                        }
                        
                        this.totalBedsAll = this.wards.reduce((sum, w) => sum + w.total_beds, 0);
                        this.totalOccupiedAll = this.wards.reduce((sum, w) => sum + w.occupied_beds, 0);
                        this.totalAvailableAll = this.wards.reduce((sum, w) => sum + w.available_beds, 0);
                        this.overallOccupancyRate = this.totalBedsAll > 0 ? Math.round((this.totalOccupiedAll / this.totalBedsAll) * 100) : 0;
                        this.criticalWardsCount = this.wards.filter(w => w.is_critical).length;
                        
                    } catch (error) {
                        console.error('Error loading wards:', error);
                        this.error = error.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                async deleteWard(wardId) {
                    if (!confirm('Are you sure you want to delete this ward? All beds and patient assignments will be removed.')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/wards/${wardId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Ward deleted successfully!');
                            window.location.reload();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to delete ward'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error deleting ward: ' + error.message);
                    }
                }
            };
        }
    </script>
</x-app-layout>