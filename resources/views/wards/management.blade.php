<x-app-layout>
    <div class="min-h-screen bg-gray-100 p-8"
         x-data="{ activeTab: 'overview' }">

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">🏥 Hospital Ward & Bed Management</h1>
                <p class="text-gray-500 mt-1">Manage patient beds and ward occupancy</p>
            </div>

            <!-- Tabs -->
            <div class="flex gap-2 mb-8 border-b border-gray-200">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        :style="activeTab === 'overview' ? 'background-color: #83D475;' : ''"
                        class="px-8 py-3 rounded-t-xl font-bold text-sm transition">
                    🏨 Ward Overview
                </button>
                <button @click="activeTab = 'admission'" 
                        :class="activeTab === 'admission' ? 'text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        :style="activeTab === 'admission' ? 'background-color: #83D475;' : ''"
                        class="px-8 py-3 rounded-t-xl font-bold text-sm transition">
                    👤 Patient Admission
                </button>
            </div>

            <!-- TAB 1: Ward Overview -->
            <div x-show="activeTab === 'overview'" x-cloak>
                
                <!-- SUMMARY STATISTICS CARDS - Dashboard Style -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Beds Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Total Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2">{{ $totalBedsAll }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Occupied Beds Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Occupied Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2">{{ $totalOccupiedAll }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Vacant Beds Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Vacant Beds</p>
                                <p class="text-3xl font-black text-gray-800 mt-2">{{ $totalAvailableAll }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Occupancy Rate Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-left-color: #83D475;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Occupancy Rate</p>
                                <p class="text-3xl font-black text-gray-800 mt-2">{{ $overallOccupancyRate }}%</p>
                            </div>
                            <div class="bg-gray-50 rounded-full p-3">
                                <svg class="w-8 h-8" style="color: #83D475;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Critical Alert -->
                @if($criticalWardsCount > 0)
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-500 rounded-full p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-red-800 font-bold">Critical Occupancy Alert</p>
                            <p class="text-red-600 text-sm">{{ $criticalWardsCount }} ward(s) are at or above 90% occupancy. Please review bed allocation.</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Wards Grid - Dashboard Style Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($wards as $ward)
                        @php
                            $total = $ward->total_beds;
                            $occupied = $ward->occupied_beds;
                            $available = $ward->available_beds;
                            $occupancy = $total > 0 ? round(($occupied / $total) * 100) : 0;
                            $criticalCount = $occupancy >= 90 ? ($total - $available) : 0;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 border-2 border-gray-300">
                            <div class="px-5 pt-5 pb-3 border-b border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-black text-gray-800">{{ $ward->ward_name }}</h3>
                                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mt-1">
                                            Floor {{ $ward->floor ?? '1' }} • {{ $ward->ward_type ?? 'General' }}
                                        </p>
                                    </div>
                                    @if($occupancy >= 90)
                                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full">CRITICAL</span>
                                    @elseif($occupancy >= 70)
                                        <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full">WARNING</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-5">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Total Beds</p>
                                        <p class="text-2xl font-black text-gray-800">{{ $total }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Occupied</p>
                                        <p class="text-2xl font-black text-gray-800">{{ $occupied }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Vacant</p>
                                        <p class="text-2xl font-black text-gray-800">{{ $available }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Critical</p>
                                        <p class="text-2xl font-black text-gray-800">{{ $criticalCount }}</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span class="font-bold">Occupancy Rate</span>
                                        <span class="font-bold text-gray-800">{{ $occupancy }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-500 {{ $occupancy >= 90 ? 'bg-red-500' : ($occupancy >= 70 ? 'bg-orange-500' : 'bg-green-500') }}" 
                                             style="width: {{ $occupancy }}%"></div>
                                    </div>
                                    <p class="text-right text-[10px] text-gray-400 mt-1">
                                        {{ $available }} bed(s) vacant • {{ $occupied }} occupied
                                    </p>
                                </div>
                                
                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('wards.show', $ward->ward_id) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-sm">
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
                                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl font-bold text-xs text-center transition duration-200 shadow-sm">
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
                
                <!-- ADD NEW WARD BUTTON -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('wards.create') }}" 
                       style="background-color: #83D475; color: white;"
                       class="px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:opacity-90 transition shadow-md inline-flex items-center gap-2">
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
                        patient_id: '',
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
                        if(!this.admissionForm.patient_id) return alert('Please select a patient');
                        if(!this.admissionForm.diagnosis) return alert('Please enter diagnosis');
                        if(!this.admissionForm.bed_id) return alert('Please select a bed');
                        if(!this.admissionForm.ward_id) return alert('Please select a valid bed');
                        
                        try {
                            const response = await axios.post('/patients/admit-existing', {
                                patient_id: this.admissionForm.patient_id,
                                diagnosis: this.admissionForm.diagnosis,
                                condition: this.admissionForm.condition,
                                bed_id: this.admissionForm.bed_id,
                                ward_id: this.admissionForm.ward_id
                            });
                            
                            if (response.data.success) {
                                alert('Patient admitted successfully!');
                                this.admissionForm = {
                                    patient_id: '',
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
                        <div class="bg-white rounded-2xl shadow-sm">
                            <div class="px-6 py-4 rounded-t-2xl" style="background-color: #83D475;">
                                <h2 class="text-xl font-black text-white">👤 Patient Admission</h2>
                                <p class="text-white text-sm opacity-90">Assign existing patient to a bed</p>
                            </div>
                            
                            <div class="p-6 max-h-[60vh] overflow-y-auto">
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Select Patient *</label>
                                        <select x-model="admissionForm.patient_id" 
                                                class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 p-3">
                                            <option value="">-- Select a patient --</option>
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->patient_id }}">
                                                    {{ $patient->first_name }} {{ $patient->last_name }} (ID: {{ $patient->patient_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Select an existing patient from the list</p>
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
                                                    {{ $bed->bed_number }} - {{ $bed->ward->ward_name ?? 'Unknown Ward' }}
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
                                        class="w-full text-white font-black py-4 px-4 rounded-xl transition duration-200 shadow-md text-base"
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
