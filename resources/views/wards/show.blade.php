<x-app-layout>
    <div class="min-h-screen bg-[#f0f4f8] p-8" 
         x-data="{ 
            showModal: false, 
            showAddBedModal: false,
            isAssigning: false,
            isEditing: false,
            selectedBed: null,
            patientId: '', 
            diagnosis: '',
            filter: 'all',
            newBedName: '',
            updatedStatus: '',
            editForm: {
                patient_name: '',
                diagnosis: '',
                condition: ''
            },

            openBedDetails(bed) {
                this.selectedBed = bed;
                this.updatedStatus = bed.is_available ? 'available' : 'occupied'; 
                this.isAssigning = false;
                this.isEditing = false;
                this.patientId = '';
                this.diagnosis = '';
                // Populate edit form with current patient data
                if (bed.current_inpatient) {
                    this.editForm.patient_name = bed.current_inpatient.patient?.patient_name || '';
                    this.editForm.diagnosis = bed.current_inpatient.primary_diagnosis || '';
                    this.editForm.condition = bed.current_inpatient.condition || 'Stable';
                }
                this.showModal = true;
            },

            async updateBedStatus() {
                try {
                    const response = await axios.post(`/beds/${this.selectedBed.bed_id}/status`, { 
                        status: this.updatedStatus 
                    });
                    if (response.data.success) window.location.reload();
                } catch (error) {
                    alert(error.response?.data?.message || 'Error updating status');
                    this.updatedStatus = this.selectedBed.is_available ? 'available' : 'occupied';
                }
            },

            async discharge() {
                if(!confirm('Are you sure you want to discharge this patient?')) return;
                try {
                    const response = await axios.post(`/beds/${this.selectedBed.bed_id}/update`, { action: 'discharge' });
                    if (response.data.success) window.location.reload();
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to discharge'));
                }
            },

            async confirmAssignment() {
                if(!this.patientId) return alert('Please enter a Patient ID.');
                try {
                    const response = await axios.post(`/beds/${this.selectedBed.bed_id}/update`, { 
                        action: 'assign', 
                        patient_id: this.patientId,
                        diagnosis: this.diagnosis
                    });
                    if (response.data.success) window.location.reload();
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to assign'));
                }
            },

            async updatePatientInfo() {
                if(!this.editForm.patient_name) return alert('Please enter patient name');
                try {
                    const response = await axios.post(`/patients/${this.selectedBed.current_inpatient.patient_id}/update`, {
                        patient_name: this.editForm.patient_name,
                        diagnosis: this.editForm.diagnosis,
                        condition: this.editForm.condition
                    });
                    if (response.data.success) {
                        alert('Patient information updated successfully!');
                        this.isEditing = false;
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to update patient info'));
                }
            },

            async addBed() {
                if(!this.newBedName) return alert('Please enter a bed name.');
                try {
                    const response = await axios.post(`/wards/{{ $ward->ward_id }}/beds`, { 
                        bed_name: this.newBedName 
                    });
                    if (response.data.success) window.location.reload();
                } catch (error) {
                    alert('Error adding bed: ' + (error.response?.data?.message || 'Database connection error'));
                }
            },

            async deleteBed(bedId) {
                if(!confirm('Are you sure you want to delete this bed? Any patient assigned will also be discharged.')) return;
                try {
                    const response = await axios.delete(`/beds/${bedId}`);
                    if (response.data.success) {
                        alert('Bed deleted successfully!');
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to delete bed'));
                }
            }
         }">

        <div class="max-w-7xl mx-auto">
            <!-- Header & Actions -->
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('wards.management') }}" class="text-blue-600 font-bold hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Wards
                </a>
                <button @click="showAddBedModal = true" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg transition">
                    + Add New Bed
                </button>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ $ward->ward_name }} – Bed Management</h1>
                <p class="text-gray-500 font-medium">Manage patient beds (Capacity: {{ $ward->total_beds }})</p>
            </div>

            <!-- Stats/Filter Tabs -->
            <div class="flex flex-wrap gap-3 mb-10">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                    All ({{ $stats['all'] }})
                </button>
                <button @click="filter = 'available'" :class="filter === 'available' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                    Available ({{ $stats['available'] }})
                </button>
                <button @click="filter = 'occupied'" :class="filter === 'occupied' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                    Occupied ({{ $stats['occupied'] }})
                </button>
            </div>

            <!-- Bed Grid with Delete Button -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($beds as $bed)
                    <div class="relative group">
                        <div x-show="filter === 'all' || (filter === 'available' && {{ $bed->is_available ? 'true' : 'false' }}) || (filter === 'occupied' && {{ !$bed->is_available ? 'true' : 'false' }})"
                             @click="openBedDetails({{ json_encode($bed->load('currentInpatient.patient')) }})"
                             class="cursor-pointer rounded-2xl p-5 border transition-all hover:shadow-xl group"
                             :class="{{ $bed->is_available ? 'true' : 'false' }} ? 'bg-white border-gray-100' : 'bg-[#e3f2fd] border-blue-200'">
                            
                            <div class="flex justify-between items-start mb-4">
                                <span class="text-xl font-black text-blue-900 leading-none">{{ $bed->bed_name }}</span>
                                <div :class="{{ $bed->is_available ? 'true' : 'false' }} ? 'bg-green-500' : 'bg-blue-600'" class="w-2 h-2 rounded-full"></div>
                            </div>

                            <p class="text-[10px] font-black uppercase tracking-widest mb-4 {{ $bed->is_available ? 'text-green-500' : 'text-blue-600' }}">
                                {{ $bed->is_available ? 'Available' : 'Occupied' }}
                            </p>

                            <div class="min-h-[40px]">
                                @if(!$bed->is_available && $bed->currentInpatient)
                                    <p class="text-sm font-black text-blue-900 truncate">{{ $bed->currentInpatient->patient->patient_name ?? 'Unknown' }}</p>
                                    <p class="text-[11px] text-gray-500 font-bold truncate">{{ $bed->currentInpatient->primary_diagnosis ?? 'Standard Care' }}</p>
                                @else
                                    <p class="text-xs font-bold text-gray-300 italic">No Patient</p>
                                @endif
                            </div>
                        </div>
                        <!-- Delete Button on each bed card -->
                        <button @click.stop="deleteBed({{ $bed->bed_id }})" 
                                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md"
                                title="Delete bed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- BED DETAILS MODAL (same as before) -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-lg rounded-[1.5rem] shadow-2xl overflow-hidden" @click.away="showModal = false">
                
                <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">
                            <span x-text="selectedBed?.bed_name"></span> Details
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Bed Management System</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>

                <div class="p-8">
                    <!-- View Mode -->
                    <div x-show="!isAssigning && !isEditing">
                        <div class="mb-8">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Status</label>
                            <select x-model="updatedStatus" @change="updateBedStatus()" 
                                    class="w-full bg-white border border-gray-200 rounded-xl p-3 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>

                        <div x-show="updatedStatus === 'occupied' && selectedBed?.current_inpatient" class="space-y-6">
                            <div class="border-t pt-6">
                                <h4 class="text-md font-black text-gray-800 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Patient Information
                                </h4>
                                
                                <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Patient Name</span>
                                        <p class="font-black text-gray-900 text-lg" x-text="selectedBed?.current_inpatient?.patient?.patient_name || 'N/A'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Age</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.patient?.age || 'N/A'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Admission Date</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.admission_date ? new Date(selectedBed.current_inpatient.admission_date).toLocaleDateString() : 'N/A'"></p>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Diagnosis</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.primary_diagnosis || 'Standard Care'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Patient ID</span>
                                        <p class="font-black text-gray-900" x-text="'P' + (selectedBed?.current_inpatient?.patient_id || '----')"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Condition</span>
                                        <p class="font-black text-green-600" x-text="selectedBed?.current_inpatient?.condition || 'Stable'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = true" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-blue-700 transition shadow-lg">
                                    Edit Patient Info
                                </button>
                                <button @click="discharge()" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-red-600 transition shadow-lg">
                                    Discharge Patient
                                </button>
                            </div>
                        </div>

                        <div x-show="updatedStatus === 'occupied' && !selectedBed?.current_inpatient" class="bg-amber-50 border border-amber-200 p-6 rounded-2xl mb-4 text-center">
                            <div class="text-amber-500 mb-2">
                                <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h4 class="font-black text-amber-800 text-sm uppercase tracking-tight">Inconsistent Data Detected</h4>
                            <p class="text-xs text-amber-600 font-medium mb-4">This bed is marked as 'Occupied' but has no assigned patient record.</p>
                            <button @click="isAssigning = true" class="w-full bg-amber-600 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-700 transition">
                                Assign Patient Now
                            </button>
                        </div>

                        <div x-show="updatedStatus === 'available'" class="text-center py-8">
                            <div class="bg-green-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h4 class="font-black text-gray-800 text-lg mb-2">Bed Available</h4>
                            <p class="text-gray-500 text-sm mb-6">This bed is currently available. No patient assigned.</p>
                            <button @click="isAssigning = true" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-xl transition">
                                + Assign Patient
                            </button>
                        </div>
                    </div>

                    <!-- Edit Patient Mode -->
                    <div x-show="isEditing" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">Edit Patient Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Patient Name</label>
                                <input type="text" x-model="editForm.patient_name" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800">
                            </div>
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Diagnosis</label>
                                <input type="text" x-model="editForm.diagnosis" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800" placeholder="Enter diagnosis">
                            </div>
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Condition</label>
                                <select x-model="editForm.condition" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800">
                                    <option value="Stable">Stable</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Fair">Fair</option>
                                </select>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = false" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-black text-[11px] uppercase hover:bg-gray-300 transition">
                                    Cancel
                                </button>
                                <button @click="updatePatientInfo()" 
                                        class="flex-1 text-white py-3 rounded-xl font-black text-[11px] uppercase shadow-lg transition hover:opacity-90"
                                        style="background-color: #83D475;">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Patient Mode -->
                    <div x-show="isAssigning" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">New Admission</h4>
                        <div class="mb-4">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Enter Patient ID</label>
                            <input type="text" x-model="patientId" class="w-full border-gray-100 bg-gray-50 rounded-xl p-4 font-bold" placeholder="e.g., 1002">
                        </div>
                        <div class="mb-4">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Primary Diagnosis (Optional)</label>
                            <input type="text" x-model="diagnosis" class="w-full border-gray-100 bg-gray-50 rounded-xl p-4 font-bold" placeholder="e.g., Post-operative care">
                        </div>
                        <div class="flex gap-3">
                            <button @click="isAssigning = false; patientId = ''; diagnosis = ''" class="flex-1 bg-gray-100 text-gray-400 py-4 rounded-xl font-black text-[10px] uppercase">Cancel</button>
                            <button @click="confirmAssignment()" class="flex-1 bg-blue-600 text-white py-4 rounded-xl font-black text-[10px] uppercase shadow-lg">Confirm Admission</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD BED MODAL -->
        <div x-show="showAddBedModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden" @click.away="showAddBedModal = false">
                <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-800">Add New Bed</h3>
                    <button @click="showAddBedModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-8">
                    <div class="mb-6">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Name/Number</label>
                        <input type="text" x-model="newBedName" placeholder="e.g., B1-106" class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="flex gap-3">
                        <button @click="showAddBedModal = false" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition">Cancel</button>
                        <button @click="addBed()" class="flex-1 bg-blue-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 shadow-lg transition">Create Bed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>