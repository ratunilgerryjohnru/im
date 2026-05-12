<x-app-layout>
    <div class="min-h-screen bg-[#f0f4f8] p-8" 
         x-data="{ 
            showModal: false, 
            showAddBedModal: false,
            showEditBedModal: false,
            isAssigning: false,
            isEditing: false,
            selectedBed: null,
            selectedPatientId: '', 
            selectedPatientName: '',
            diagnosis: '',
            condition: 'Stable',
            filter: 'all',
            newBedName: '',
            newBedType: '',
            editBedForm: {
                bed_id: null,
                bed_number: '',
                bed_type: ''
            },
            nonAdmittedPatients: {{ Illuminate\Support\Facades\DB::table('patient')
                ->whereNotIn('patient_id', function($query) {
                    $query->select('patient_id')->from('in_patient');
                })
                ->orderBy('first_name')
                ->get(['patient_id', 'first_name', 'last_name']) }},
            editForm: {
                diagnosis: '',
                condition: ''
            },

            openBedDetails(bed) {
                this.selectedBed = bed;
                this.isAssigning = false;
                this.isEditing = false;
                this.selectedPatientId = '';
                this.selectedPatientName = '';
                this.diagnosis = '';
                this.condition = 'Stable';
                if (bed.current_inpatient) {
                    this.editForm.diagnosis = bed.current_inpatient.primary_diagnosis || '';
                    this.editForm.condition = bed.current_inpatient.condition || 'Stable';
                }
                this.showModal = true;
            },

            openEditBedModal(bedId, bedNumber, bedType) {
                this.editBedForm.bed_id = bedId;
                this.editBedForm.bed_number = bedNumber;
                this.editBedForm.bed_type = bedType || '';
                this.showEditBedModal = true;
            },

            async updateBed() {
                if(!this.editBedForm.bed_number) return alert('Please enter a bed number');
                try {
                    const response = await axios.put(`/beds/${this.editBedForm.bed_id}`, {
                        bed_number: this.editBedForm.bed_number,
                        bed_type: this.editBedForm.bed_type
                    });
                    if (response.data.success) {
                        alert('Bed updated successfully!');
                        this.showEditBedModal = false;
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to update bed'));
                }
            },

            calculateAge(dob) {
                if (!dob) return 'N/A';
                const birthDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age + ' years';
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
                if(!this.selectedPatientId) return alert('Please select a patient');
                if(!this.diagnosis) return alert('Please enter a diagnosis');
                if(!this.condition) return alert('Please select a condition');
                try {
                    const response = await axios.post(`/beds/${this.selectedBed.bed_id}/update`, { 
                        action: 'assign', 
                        patient_id: this.selectedPatientId,
                        diagnosis: this.diagnosis,
                        condition: this.condition
                    });
                    if (response.data.success) window.location.reload();
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to assign'));
                }
            },

            async updateClinicalInfo() {
                if(!this.editForm.diagnosis) return alert('Please enter a diagnosis');
                try {
                    const response = await axios.post(`/patients/${this.selectedBed.current_inpatient.patient_id}/update-clinical`, {
                        diagnosis: this.editForm.diagnosis,
                        condition: this.editForm.condition
                    });
                    if (response.data.success) {
                        alert('Clinical information updated successfully!');
                        this.isEditing = false;
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Error: ' + (error.response?.data?.message || 'Failed to update clinical info'));
                }
            },

            async addBed() {
                if(!this.newBedName) return alert('Please enter a bed name.');
                try {
                    const response = await axios.post(`/wards/{{ $ward->ward_id }}/beds`, { 
                        bed_number: this.newBedName,
                        bed_type: this.newBedType
                    });
                    if (response.data.success) {
                        this.newBedName = '';
                        this.newBedType = '';
                        window.location.reload();
                    }
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

            <!-- Bed Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($beds as $bed)
                    @php
                        $hasPatient = $bed->currentInpatient !== null;
                        $displayStatus = $hasPatient ? 'Occupied' : 'Available';
                        $statusColor = $hasPatient ? 'text-blue-600' : 'text-green-500';
                        $dotColor = $hasPatient ? 'bg-blue-600' : 'bg-green-500';
                        $bgClass = $hasPatient ? 'bg-[#e3f2fd] border-blue-200' : 'bg-white border-gray-100';
                        $bedJson = json_encode($bed->load('currentInpatient.patient'));
                    @endphp
                    
                    <template x-if="filter === 'all' || (filter === 'available' && {{ $hasPatient ? 'false' : 'true' }}) || (filter === 'occupied' && {{ $hasPatient ? 'true' : 'false' }})">
                        <div class="relative group">
                            <div @click="openBedDetails({{ $bedJson }})"
                                 class="cursor-pointer rounded-2xl p-5 border transition-all hover:shadow-xl {{ $bgClass }}">
                                
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-xl font-black text-blue-900 leading-none">{{ $bed->bed_number }}</span>
                                    <div class="w-2 h-2 rounded-full {{ $dotColor }}"></div>
                                </div>

                                <p class="text-[10px] font-black uppercase tracking-widest mb-4 {{ $statusColor }}">
                                    {{ $displayStatus }}
                                </p>

                                <div class="min-h-[40px]">
                                    @if($hasPatient)
                                        <p class="text-sm font-black text-blue-900 truncate">
                                            {{ $bed->currentInpatient->patient->first_name ?? '' }} 
                                            {{ $bed->currentInpatient->patient->last_name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 font-bold truncate">
                                            {{ $bed->currentInpatient->primary_diagnosis ?? 'Standard Care' }}
                                        </p>
                                    @else
                                        <p class="text-xs font-bold text-gray-300 italic">No Patient</p>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 flex gap-1">
                                <!-- Edit Bed Button -->
                                <button @click.stop="openEditBedModal({{ $bed->bed_id }}, '{{ $bed->bed_number }}', '{{ $bed->bed_type }}')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md"
                                        title="Edit bed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Delete Bed Button -->
                                <button @click.stop="deleteBed({{ $bed->bed_id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md"
                                        title="Delete bed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>
        </div>

        <!-- BED DETAILS MODAL -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-lg rounded-[1.5rem] shadow-2xl overflow-hidden" @click.away="showModal = false">
                
                <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">
                            <span x-text="selectedBed?.bed_number"></span> Details
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Bed Management System</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>

                <div class="p-8">
                    <!-- View Mode -->
                    <div x-show="!isAssigning && !isEditing">
                        
                        <!-- BED STATUS - NO DROPDOWN, JUST PLAIN TEXT -->
                        <div class="mb-8">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Status</label>
                            
                            <template x-if="!selectedBed?.current_inpatient">
                                <div class="w-full bg-green-50 border border-green-200 rounded-xl p-3 font-bold text-green-700 flex items-center justify-between">
                                    <span>Available</span>
                                    <span class="text-xs font-normal text-green-600">✓ Ready for admission</span>
                                </div>
                            </template>
                            
                            <template x-if="selectedBed?.current_inpatient">
                                <div class="w-full bg-blue-50 border border-blue-200 rounded-xl p-3 font-bold text-blue-700 flex items-center justify-between">
                                    <span>Occupied</span>
                                    <span class="text-xs font-normal text-blue-600">👤 Patient assigned</span>
                                </div>
                            </template>
                        </div>

                        <!-- Patient Information (only shows when occupied) -->
                        <div x-show="selectedBed?.current_inpatient" class="space-y-6">
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
                                        <p class="font-black text-gray-900 text-lg" x-text="(selectedBed?.current_inpatient?.patient?.first_name || '') + ' ' + (selectedBed?.current_inpatient?.patient?.last_name || 'N/A')"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Age</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.patient?.dob ? calculateAge(selectedBed.current_inpatient.patient.dob) : 'N/A'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Gender</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.patient?.sex || 'N/A'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Admission Date</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.date_admitted ? new Date(selectedBed.current_inpatient.date_admitted).toLocaleDateString() : 'N/A'"></p>
                                    </div>
                                    
                                    <div>
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Patient ID</span>
                                        <p class="font-black text-gray-900" x-text="'P' + (selectedBed?.current_inpatient?.patient_id || '----')"></p>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Diagnosis</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.primary_diagnosis || 'Standard Care'"></p>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Condition</span>
                                        <p class="font-black text-green-600" x-text="selectedBed?.current_inpatient?.condition || 'Stable'"></p>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Phone</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.patient?.phone || 'N/A'"></p>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <span class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase">Address</span>
                                        <p class="font-black text-gray-900" x-text="selectedBed?.current_inpatient?.patient?.address || 'N/A'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = true" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-blue-700 transition shadow-lg">
                                    Update Clinical Info
                                </button>
                                <button @click="discharge()" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-red-600 transition shadow-lg">
                                    Discharge Patient
                                </button>
                            </div>
                        </div>

                        <!-- Show Assign Patient button for available beds -->
                        <div x-show="!selectedBed?.current_inpatient" class="text-center py-8">
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

                    <!-- Update Clinical Info Mode -->
                    <div x-show="isEditing" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">Update Clinical Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Diagnosis</label>
                                <input type="text" x-model="editForm.diagnosis" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800" placeholder="Enter diagnosis">
                                <p class="text-xs text-gray-400 mt-1">Primary medical diagnosis</p>
                            </div>
                            
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Condition</label>
                                <select x-model="editForm.condition" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800">
                                    <option value="Stable">Stable</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Fair">Fair</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Patient's current medical condition</p>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-4">
                                <p class="text-xs text-blue-700 font-medium">
                                    <span class="font-black">ℹ️ Note:</span> Patient personal information (name, contact, address) cannot be edited here. 
                                    Please contact registration desk for demographic updates.
                                </p>
                            </div>
                            
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = false" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-black text-[11px] uppercase hover:bg-gray-300 transition">
                                    Cancel
                                </button>
                                <button @click="updateClinicalInfo()" 
                                        class="flex-1 text-white py-3 rounded-xl font-black text-[11px] uppercase shadow-lg transition hover:opacity-90"
                                        style="background-color: #83D475;">
                                    Save Clinical Updates
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Patient Mode -->
                    <div x-show="isAssigning" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">Assign Patient to Bed</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Select Patient *</label>
                                <select x-model="selectedPatientId" 
                                        class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800">
                                    <option value="">-- Select a patient --</option>
                                    <template x-for="patient in nonAdmittedPatients" :key="patient.patient_id">
                                        <option :value="patient.patient_id" x-text="patient.first_name + ' ' + patient.last_name + ' (ID: ' + patient.patient_id + ')'"></option>
                                    </template>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Only new patients (never admitted) are shown</p>
                            </div>
                            
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Diagnosis *</label>
                                <input type="text" x-model="diagnosis" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800" placeholder="Enter diagnosis">
                            </div>
                            
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Condition</label>
                                <select x-model="condition" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3 font-bold text-gray-800">
                                    <option value="Stable">Stable</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Fair">Fair</option>
                                </select>
                            </div>
                            
                            <div class="flex gap-3 pt-4">
                                <button @click="isAssigning = false; selectedPatientId = ''; diagnosis = ''; condition = 'Stable'" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-black text-[11px] uppercase hover:bg-gray-300 transition">
                                    Cancel
                                </button>
                                <button @click="confirmAssignment()" 
                                        class="flex-1 text-white py-3 rounded-xl font-black text-[11px] uppercase shadow-lg transition hover:opacity-90"
                                        style="background-color: #83D475;">
                                    Assign Patient
                                </button>
                            </div>
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
                    <div class="space-y-6">
                        <div>
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Number</label>
                            <input type="text" x-model="newBedName" placeholder="e.g., B1-106" 
                                   class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Type</label>
                            <input type="text" x-model="newBedType" placeholder="e.g., Standard, ICU, Pediatric" 
                                   class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button @click="showAddBedModal = false" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition">Cancel</button>
                        <button @click="addBed()" class="flex-1 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition hover:opacity-90" 
                                style="background-color: #83D475;">Create Bed</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT BED MODAL -->
        <div x-show="showEditBedModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden" @click.away="showEditBedModal = false">
                <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-black text-gray-800">Edit Bed</h3>
                    <button @click="showEditBedModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-8">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Number</label>
                            <input type="text" x-model="editBedForm.bed_number" placeholder="e.g., B1-106" 
                                   class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Type</label>
                            <input type="text" x-model="editBedForm.bed_type" placeholder="e.g., Standard, ICU, Pediatric" 
                                   class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button @click="showEditBedModal = false" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition">Cancel</button>
                        <button @click="updateBed()" class="flex-1 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition hover:opacity-90" 
                                style="background-color: #83D475;">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        .grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        
        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        
        .relative.group > div {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
    </style>
</x-app-layout>