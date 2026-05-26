<x-app-layout>
    <div class="min-h-screen bg-[#f0f4f8] p-8" x-data="wardManagement()" x-init="init({{ $ward->ward_id }})">
        
        <div class="max-w-7xl mx-auto">
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-20">
                <p class="text-gray-500 text-lg">Loading bed management data...</p>
            </div>
            
            <div x-show="!loading">
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
                    <h1 class="text-3xl font-black text-gray-900 leading-tight" x-text="wardName + ' – Bed Management'"></h1>
                    <p class="text-gray-500 font-medium" x-text="'Manage patient beds'"></p>
                </div>

                <!-- Stats/Filter Tabs -->
                <div class="flex flex-wrap gap-3 mb-10">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                        All (<span x-text="stats.all"></span>)
                    </button>
                    <button @click="filter = 'available'" :class="filter === 'available' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                        Available (<span x-text="stats.available"></span>)
                    </button>
                    <button @click="filter = 'occupied'" :class="filter === 'occupied' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200'" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                        Occupied (<span x-text="stats.occupied"></span>)
                    </button>
                </div>

                <!-- Bed Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <template x-for="bed in filteredBeds" :key="bed.bed_id">
                        <div class="relative group">
                            <div @click="openBedDetails(bed)" class="cursor-pointer rounded-2xl p-5 border transition-all hover:shadow-xl" :class="bed.current_inpatient ? 'bg-[#e3f2fd] border-blue-200' : 'bg-white border-gray-100'">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-xl font-black text-blue-900 leading-none" x-text="bed.bed_number"></span>
                                    <div class="w-2 h-2 rounded-full" :class="bed.current_inpatient ? 'bg-blue-600' : 'bg-green-500'"></div>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-widest mb-4" :class="bed.current_inpatient ? 'text-blue-600' : 'text-green-500'" x-text="bed.current_inpatient ? 'OCCUPIED' : 'AVAILABLE'"></p>
                                <div class="min-h-[40px]">
                                    <template x-if="bed.current_inpatient">
                                        <div>
                                            <p class="text-sm font-black text-blue-900 truncate" x-text="(bed.current_inpatient.patient?.first_name || '') + ' ' + (bed.current_inpatient.patient?.last_name || 'Unknown')"></p>
                                            <p class="text-[11px] text-gray-500 font-bold truncate" x-text="bed.current_inpatient.primary_diagnosis || 'Standard Care'"></p>
                                        </div>
                                    </template>
                                    <template x-if="!bed.current_inpatient">
                                        <p class="text-xs font-bold text-gray-300 italic">No Patient</p>
                                    </template>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 flex gap-1">
                                <button @click.stop="openEditBedModal(bed.bed_id, bed.bed_number, bed.bed_type)" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button @click.stop="deleteBed(bed.bed_id)" class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="filteredBeds.length === 0" class="text-center py-20">
                    <p class="text-gray-500 text-lg">No beds match the selected filter.</p>
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
                            <input type="text" x-model="editBedForm.bed_number" class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700">
                        </div>
                        <div>
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Bed Type</label>
                            <input type="text" x-model="editBedForm.bed_type" class="w-full bg-white border border-gray-200 rounded-xl p-4 font-bold text-gray-700">
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button @click="showEditBedModal = false" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition">Cancel</button>
                        <button @click="updateBed()" class="flex-1 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition" style="background-color: #83D475;">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- BED DETAILS MODAL -->
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-lg rounded-[1.5rem] shadow-2xl overflow-hidden" @click.away="showModal = false">
                <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800"><span x-text="selectedBed?.bed_number"></span> Details</h3>
                        <p class="text-xs text-gray-500 mt-1">Bed Management System</p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-3xl leading-none">&times;</button>
                </div>
                <div class="p-8">
                    <div x-show="!isAssigning && !isEditing">
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
                        
                        <div x-show="selectedBed?.current_inpatient" class="space-y-6">
                            <div class="border-t pt-6">
                                <h4 class="text-md font-black text-gray-800 mb-6">Patient Information</h4>
                                <div class="space-y-2">
                                    <p><strong>Patient:</strong> <span x-text="(selectedBed?.current_inpatient?.patient?.first_name || '') + ' ' + (selectedBed?.current_inpatient?.patient?.last_name || 'N/A')"></span></p>
                                    <p><strong>Diagnosis:</strong> <span x-text="selectedBed?.current_inpatient?.primary_diagnosis || 'Standard Care'"></span></p>
                                    <p><strong>Admitted:</strong> <span x-text="selectedBed?.current_inpatient?.date_admitted ? new Date(selectedBed.current_inpatient.date_admitted).toLocaleDateString() : 'N/A'"></span></p>
                                </div>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = true" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-black text-[11px] uppercase hover:bg-blue-700 transition">Update Clinical Info</button>
                                <button @click="discharge()" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-black text-[11px] uppercase hover:bg-red-600 transition">Discharge Patient</button>
                            </div>
                        </div>
                        
                        <div x-show="!selectedBed?.current_inpatient" class="text-center py-8">
                            <div class="bg-green-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <h4 class="font-black text-gray-800 text-lg mb-2">Bed Available</h4>
                            <button @click="isAssigning = true" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-xs uppercase hover:bg-blue-700 transition">+ Assign Patient</button>
                        </div>
                    </div>
                    
                    <div x-show="isEditing" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">Update Clinical Information</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Diagnosis</label>
                                <input type="text" x-model="editForm.diagnosis" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3">
                            </div>
                            <div>
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Condition</label>
                                <select x-model="editForm.condition" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3">
                                    <option value="Stable">Stable</option>
                                    <option value="Critical">Critical</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Fair">Fair</option>
                                </select>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button @click="isEditing = false" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl">Cancel</button>
                                <button @click="updateClinicalInfo()" class="flex-1 text-white py-3 rounded-xl" style="background-color: #83D475;">Save Changes</button>
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="isAssigning" x-transition>
                        <h4 class="font-black text-gray-800 text-lg mb-4">Assign Patient to Bed</h4>
                        <div class="space-y-4">
                            <select x-model="selectedPatientId" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3">
                                <option value="">-- Select Patient --</option>
                                <template x-for="patient in nonAdmittedPatients" :key="patient.patient_id">
                                    <option :value="patient.patient_id" x-text="patient.first_name + ' ' + patient.last_name + ' (ID: ' + patient.patient_id + ')'"></option>
                                </template>
                            </select>
                            <input type="text" x-model="diagnosis" placeholder="Diagnosis" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3">
                            <select x-model="condition" class="w-full border-gray-200 bg-gray-50 rounded-xl p-3">
                                <option value="Stable">Stable</option>
                                <option value="Critical">Critical</option>
                                <option value="Serious">Serious</option>
                                <option value="Fair">Fair</option>
                            </select>
                            <div class="flex gap-3 pt-4">
                                <button @click="isAssigning = false" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl">Cancel</button>
                                <button @click="confirmAssignment()" class="flex-1 text-white py-3 rounded-xl" style="background-color: #83D475;">Assign</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD BED MODAL -->
        <div x-show="showAddBedModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-[#1a202c]/80 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-black text-gray-800">Add New Bed</h3>
                    <button @click="showAddBedModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-8">
                    <input type="text" x-model="newBedName" placeholder="Bed Number" class="w-full border rounded-xl p-3 mb-4">
                    <input type="text" x-model="newBedType" placeholder="Bed Type" class="w-full border rounded-xl p-3 mb-4">
                    <div class="flex gap-3">
                        <button @click="showAddBedModal = false" class="flex-1 bg-gray-200 py-3 rounded-xl">Cancel</button>
                        <button @click="addBed()" class="flex-1 text-white py-3 rounded-xl" style="background-color: #83D475;">Create Bed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) return meta.content;
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) return tokenInput.value;
            return '';
        }

        function wardManagement() {
            return {
                wardId: null,
                wardName: '',
                totalBeds: 0,
                beds: [],
                stats: { all: 0, available: 0, occupied: 0 },
                filter: 'all',
                loading: true,
                showModal: false,
                showAddBedModal: false,
                showEditBedModal: false,
                isAssigning: false,
                isEditing: false,
                selectedBed: null,
                selectedPatientId: '',
                diagnosis: '',
                condition: 'Stable',
                newBedName: '',
                newBedType: '',
                editBedForm: { bed_id: null, bed_number: '', bed_type: '' },
                editForm: { diagnosis: '', condition: '' },
                nonAdmittedPatients: [],
                
                get filteredBeds() {
                    if (this.filter === 'all') return this.beds;
                    if (this.filter === 'available') return this.beds.filter(bed => !bed.current_inpatient);
                    if (this.filter === 'occupied') return this.beds.filter(bed => bed.current_inpatient);
                    return this.beds;
                },
                
                async init(wardId) {
                    this.wardId = wardId;
                    console.log('Ward ID set to:', this.wardId);
                    await this.loadData();
                },
                
                async loadData() {
                    this.loading = true;
                    try {
                        const csrfToken = getCsrfToken();
                        
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 60000);
                        
                        const response = await fetch(`/wards/${this.wardId}/beds-data`, {
                            headers: { 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            signal: controller.signal
                        });
                        
                        clearTimeout(timeoutId);
                        
                        if (!response.ok) {
                            console.warn(`HTTP ${response.status}`);
                            this.beds = [];
                            this.stats = { all: 0, available: 0, occupied: 0 };
                            this.wardName = 'Loading...';
                            this.loading = false;
                            return;
                        }
                        
                        const data = await response.json();
                        
                        if (data && data.beds) {
                            this.beds = data.beds || [];
                            this.stats.all = data.stats?.all || 0;
                            this.stats.occupied = data.stats?.occupied || 0;
                            this.stats.available = data.stats?.available || 0;
                            this.wardName = data.ward_name || 'Ward';
                            this.totalBeds = data.total_beds || this.beds.length;
                        } else {
                            this.beds = [];
                            this.stats = { all: 0, available: 0, occupied: 0 };
                        }
                        
                        if (window.SUPABASE_URL && window.SUPABASE_KEY) {
                            this.loadNonAdmittedPatients();
                        }
                        
                    } catch (error) {
                        console.error('Error loading beds:', error);
                        this.beds = [];
                        this.stats = { all: 0, available: 0, occupied: 0 };
                    } finally {
                        this.loading = false;
                    }
                },
                
                async loadNonAdmittedPatients() {
                    try {
                        console.log('Loading non-admitted patients...');
                        
                        const patientsResponse = await fetch(`${window.SUPABASE_URL}/rest/v1/patient?select=patient_id,first_name,last_name&order=first_name.asc`, {
                            headers: { 
                                'apikey': window.SUPABASE_KEY, 
                                'Authorization': `Bearer ${window.SUPABASE_KEY}`,
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        if (!patientsResponse.ok) {
                            console.error('Patients API error:', patientsResponse.status);
                            return;
                        }
                        
                        const allPatients = await patientsResponse.json();
                        console.log('All patients count:', allPatients.length);
                        
                        const admittedResponse = await fetch(`${window.SUPABASE_URL}/rest/v1/in_patient?actual_leave=is.null&select=patient_id`, {
                            headers: { 
                                'apikey': window.SUPABASE_KEY, 
                                'Authorization': `Bearer ${window.SUPABASE_KEY}`,
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        if (!admittedResponse.ok) {
                            console.error('Admitted API error:', admittedResponse.status);
                            this.nonAdmittedPatients = allPatients;
                            return;
                        }
                        
                        const admittedArray = await admittedResponse.json();
                        const admittedIds = new Set(admittedArray.map(p => p.patient_id));
                        this.nonAdmittedPatients = allPatients.filter(p => !admittedIds.has(p.patient_id));
                        console.log('Non-admitted patients count:', this.nonAdmittedPatients.length);
                        
                    } catch (e) {
                        console.error('Error loading non-admitted patients:', e);
                        this.nonAdmittedPatients = [];
                    }
                },
                
                openBedDetails(bed) {
                    this.selectedBed = bed;
                    this.isAssigning = false;
                    this.isEditing = false;
                    this.selectedPatientId = '';
                    this.diagnosis = '';
                    this.condition = 'Stable';
                    if (bed.current_inpatient) {
                        this.editForm.diagnosis = bed.current_inpatient.primary_diagnosis || '';
                        this.editForm.condition = bed.current_inpatient.condition || 'Stable';
                        console.log('Opened occupied bed - Patient ID:', bed.current_inpatient.patient_id);
                    } else {
                        console.log('Opened available bed - no patient');
                        this.editForm.diagnosis = '';
                        this.editForm.condition = 'Stable';
                    }
                    this.showModal = true;
                },
                
                openEditBedModal(bedId, bedNumber, bedType) {
                    this.editBedForm = { bed_id: bedId, bed_number: bedNumber, bed_type: bedType || '' };
                    this.showEditBedModal = true;
                },
                
                async updateBed() {
                    if (!this.editBedForm.bed_number) {
                        alert('Please enter a bed number');
                        return;
                    }
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/beds/${this.editBedForm.bed_id}`, {
                            method: 'PUT',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                bed_number: this.editBedForm.bed_number,
                                bed_type: this.editBedForm.bed_type
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Bed updated successfully!');
                            this.showEditBedModal = false;
                            this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to update bed'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error updating bed: ' + error.message);
                    }
                },
                
                async updateClinicalInfo() {
                    console.log('=== UPDATE CLINICAL INFO ===');
                    console.log('Diagnosis:', this.editForm.diagnosis);
                    console.log('Condition:', this.editForm.condition);
                    console.log('Selected bed:', this.selectedBed);
                    
                    if (!this.editForm.diagnosis) {
                        alert('Enter a diagnosis');
                        return;
                    }
                    
                    // Check if the bed has a patient
                    if (!this.selectedBed?.current_inpatient || !this.selectedBed.current_inpatient.patient_id) {
                        alert('Cannot update clinical info: No patient is assigned to this bed. Please assign a patient first.');
                        return;
                    }
                    
                    const patientId = this.selectedBed.current_inpatient.patient_id;
                    console.log('Patient ID:', patientId);
                    
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/patients/${patientId}/update-clinical`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                diagnosis: this.editForm.diagnosis,
                                condition: this.editForm.condition
                            })
                        });
                        
                        const result = await response.json();
                        console.log('Result:', result);
                        
                        if (result.success) {
                            alert(result.message || 'Clinical information updated successfully!');
                            this.isEditing = false;
                            await this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to update'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error updating clinical info: ' + error.message);
                    }
                },
                
                async confirmAssignment() {
                    console.log('Selected patient ID:', this.selectedPatientId);
                    console.log('Diagnosis:', this.diagnosis);
                    console.log('Condition:', this.condition);
                    
                    if (!this.selectedPatientId) {
                        alert('Select a patient');
                        return;
                    }
                    if (!this.diagnosis) {
                        alert('Enter a diagnosis');
                        return;
                    }
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/beds/${this.selectedBed.bed_id}/update`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                action: 'assign', 
                                patient_id: parseInt(this.selectedPatientId), 
                                diagnosis: this.diagnosis, 
                                condition: this.condition 
                            })
                        });
                        
                        const result = await response.json();
                        console.log('Assignment result:', result);
                        
                        if (result.success) {
                            alert('Patient assigned successfully!');
                            this.showModal = false;
                            this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to assign'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error assigning patient: ' + error.message);
                    }
                },
                
                async addBed() {
                    if (!this.newBedName) {
                        alert('Enter bed number');
                        return;
                    }
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/wards/${this.wardId}/beds`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                bed_number: this.newBedName, 
                                bed_type: this.newBedType || 'Standard' 
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            this.newBedName = '';
                            this.newBedType = '';
                            this.showAddBedModal = false;
                            this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to add bed'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error adding bed: ' + error.message);
                    }
                },
                
                async deleteBed(bedId) {
                    if (!confirm('Delete this bed? This action cannot be undone.')) return;
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/beds/${bedId}`, { 
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Bed deleted successfully!');
                            this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to delete bed'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error deleting bed: ' + error.message);
                    }
                },
                
                async discharge() {
                    if (!confirm('Discharge the patient from this bed?')) return;
                    try {
                        const csrfToken = getCsrfToken();
                        const response = await fetch(`/beds/${this.selectedBed.bed_id}/update`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ action: 'discharge' })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Patient discharged successfully!');
                            this.showModal = false;
                            this.loadData();
                        } else {
                            alert('Error: ' + (result.message || 'Failed to discharge'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error discharging patient: ' + error.message);
                    }
                }
            };
        }
    </script>
</x-app-layout>