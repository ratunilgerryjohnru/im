<x-app-layout>
    <div class="min-h-screen bg-[#1a202c] p-8"
         x-data="{ activeTab: 'overview' }">

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-white">🏥 Hospital Ward & Bed Management</h1>
                <p class="text-gray-400 mt-1">Manage patient beds and ward occupancy</p>
            </div>

            <!-- Tabs with #83D475 color when active -->
            <div class="flex gap-2 mb-8 border-b border-gray-700">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                        :style="activeTab === 'overview' ? 'background-color: #83D475;' : ''"
                        class="px-8 py-3 rounded-t-xl font-bold text-sm transition">
                    🏨 Ward Overview
                </button>
                <button @click="activeTab = 'admission'" 
                        :class="activeTab === 'admission' ? 'text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                        :style="activeTab === 'admission' ? 'background-color: #83D475;' : ''"
                        class="px-8 py-3 rounded-t-xl font-bold text-sm transition">
                    👤 Patient Admission
                </button>
            </div>

            <!-- TAB 1: Ward Overview -->
            <div x-show="activeTab === 'overview'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($wards as $ward)
                        @php
                            $total = $ward->total_beds;
                            $occupied = $ward->occupied_beds;
                            $available = $ward->available_beds;
                            $occupancy = $total > 0 ? round(($occupied / $total) * 100) : 0;
                            $criticalCount = $occupancy >= 90 ? ($total - $available) : 0;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100">
                            <div class="px-5 pt-5 pb-3 border-b border-gray-100">
                                <h3 class="text-xl font-black text-gray-800">{{ $ward->ward_name }}</h3>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">
                                    Floor {{ $ward->floor ?? '1' }} • {{ $ward->ward_type ?? 'General' }}
                                </p>
                            </div>
                            
                            <div class="p-5">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                                        <p class="text-[10px] text-blue-500 font-black uppercase tracking-wider">Total Beds</p>
                                        <p class="text-2xl font-black text-blue-700">{{ $total }}</p>
                                    </div>
                                    <div class="bg-orange-50 rounded-xl p-3 text-center">
                                        <p class="text-[10px] text-orange-500 font-black uppercase tracking-wider">Occupied</p>
                                        <p class="text-2xl font-black text-orange-600">{{ $occupied }}</p>
                                    </div>
                                    <div class="bg-green-50 rounded-xl p-3 text-center">
                                        <p class="text-[10px] text-green-500 font-black uppercase tracking-wider">Available</p>
                                        <p class="text-2xl font-black text-green-600">{{ $available }}</p>
                                    </div>
                                    <div class="bg-red-50 rounded-xl p-3 text-center">
                                        <p class="text-[10px] text-red-500 font-black uppercase tracking-wider">Critical</p>
                                        <p class="text-2xl font-black text-red-600">{{ $criticalCount }}</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span class="font-bold">Occupancy Rate</span>
                                        <span class="font-bold">{{ $occupancy }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3">
                                        <div class="h-3 rounded-full transition-all duration-500 {{ $occupancy >= 90 ? 'bg-red-500' : ($occupancy >= 70 ? 'bg-orange-500' : 'bg-green-500') }}" 
                                             style="width: {{ $occupancy }}%"></div>
                                    </div>
                                    <p class="text-right text-[10px] text-gray-400 mt-1">{{ $occupancy }}% Full</p>
                                </div>
                                
                                <!-- Action Buttons with Delete -->
                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('wards.show', $ward->ward_id) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-md">
                                        View Beds
                                    </a>
                                    <a href="{{ route('wards.edit', $ward->ward_id) }}" 
                                       class="flex-1 border border-gray-200 text-gray-500 py-2.5 rounded-xl font-bold text-xs text-center hover:bg-gray-50 transition duration-200">
                                        Settings
                                    </a>
                                    <form action="{{ route('wards.destroy', $ward->ward_id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this ward? All beds in this ward will also be deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-md">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-20 bg-white rounded-2xl">
                            <p class="text-gray-500 text-lg">No wards created yet.</p>
                            <p class="text-gray-400 text-sm mt-2">Click "Add New Ward" button below to get started.</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- ADD NEW WARD BUTTON - ONLY VISIBLE IN WARD OVERVIEW TAB -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('wards.create') }}" 
                       style="background-color: #83D475; color: white;"
                       class="px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:opacity-90 transition shadow-lg inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        ADD NEW WARD
                    </a>
                </div>
            </div>

            <!-- TAB 2: Patient Admission -->
            <div x-show="activeTab === 'admission'" x-cloak
                 x-data="{
                    admissionForm: {
                        patient_name: '',
                        age: '',
                        diagnosis: '',
                        condition: 'Stable',
                        bed_id: '',
                        ward_id: ''
                    },
                    
                    async updateWardId(bedId) {
                        if (!bedId) {
                            this.admissionForm.ward_id = '';
                            return;
                        }
                        try {
                            const response = await fetch(`/api/bed-ward/${bedId}`);
                            const data = await response.json();
                            this.admissionForm.ward_id = data.ward_id;
                        } catch (error) {
                            console.error('Error fetching ward_id:', error);
                            this.admissionForm.ward_id = '';
                        }
                    },
                    
                    async submitPatientAdmission() {
                        if(!this.admissionForm.patient_name) return alert('Please enter patient name');
                        if(!this.admissionForm.age) return alert('Please enter patient age');
                        if(!this.admissionForm.diagnosis) return alert('Please enter diagnosis');
                        if(!this.admissionForm.bed_id) return alert('Please select a bed');
                        if(!this.admissionForm.ward_id) return alert('Please select a valid bed');
                        
                        try {
                            const response = await axios.post('/patients/admit', {
                                patient_name: this.admissionForm.patient_name,
                                age: this.admissionForm.age,
                                diagnosis: this.admissionForm.diagnosis,
                                condition: this.admissionForm.condition,
                                bed_id: this.admissionForm.bed_id,
                                ward_id: this.admissionForm.ward_id
                            });
                            
                            if (response.data.success) {
                                alert('Patient admitted successfully!');
                                this.admissionForm = {
                                    patient_name: '',
                                    age: '',
                                    diagnosis: '',
                                    condition: 'Stable',
                                    bed_id: '',
                                    ward_id: ''
                                };
                                window.location.reload();
                            } else {
                                alert('Error: ' + response.data.message);
                            }
                        } catch (error) {
                            console.error('Admission error:', error);
                            alert('Error: ' + (error.response?.data?.message || 'Failed to admit patient'));
                        }
                    }
                 }">
                
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl">
                        <div class="bg-white rounded-2xl shadow-xl">
                            <!-- Changed header color to #83D475 -->
                            <div class="px-6 py-4 rounded-t-2xl" style="background-color: #83D475;">
                                <h2 class="text-xl font-black text-white">👤 Patient Admission</h2>
                                <p class="text-white text-sm opacity-90">Register a new patient and assign a bed</p>
                            </div>
                            
                            <div class="p-6 max-h-[60vh] overflow-y-auto">
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Patient Name *</label>
                                        <input type="text" x-model="admissionForm.patient_name" 
                                               class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3"
                                               placeholder="Enter patient full name">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Age *</label>
                                        <input type="number" x-model="admissionForm.age" 
                                               class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3"
                                               placeholder="Enter patient age">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosis *</label>
                                        <input type="text" x-model="admissionForm.diagnosis" 
                                               class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3"
                                               placeholder="Enter diagnosis details">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Condition</label>
                                        <select x-model="admissionForm.condition" 
                                                class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3">
                                            <option value="Stable">Stable</option>
                                            <option value="Critical">Critical</option>
                                            <option value="Serious">Serious</option>
                                            <option value="Fair">Fair</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Assign Bed *</label>
                                        <select x-model="admissionForm.bed_id" 
                                                class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3"
                                                @change="updateWardId($event.target.value)">
                                            <option value="">Select a bed</option>
                                            @forelse($availableBeds as $bed)
                                                <option value="{{ $bed->bed_id }}">
                                                    {{ $bed->bed_name }} - {{ $bed->ward->ward_name ?? 'Unknown Ward' }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No available beds. Please add beds first.</option>
                                            @endforelse
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($availableBeds->count() > 0)
                                                {{ $availableBeds->count() }} available bed(s) found.
                                            @else
                                                ⚠️ No available beds! Please add beds to a ward first.
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div x-show="admissionForm.ward_id" class="text-xs text-green-600 bg-green-50 p-3 rounded-lg">
                                        ✅ Selected Ward ID: <span x-text="admissionForm.ward_id"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 pt-0 border-t border-gray-200 bg-white rounded-b-2xl">
                                <button @click="submitPatientAdmission()" 
                                        class="w-full text-white font-black py-4 px-4 rounded-xl transition duration-200 shadow-lg text-base"
                                        style="background-color: #83D475;">
                                    Admit Patient
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>