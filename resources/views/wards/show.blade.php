<x-app-layout>
    <div class="min-h-screen bg-[#f8fafc] p-8" x-data="{ 
        showModal: false, 
        isEditing: false,
        isAssigning: false,
        selectedBed: null,
        editingPatient: { patient: '', diagnosis: '', condition: 'Stable' }, 
        beds: [
            { id: '01', status: 'Occupied', patient: 'Mary Johnson', condition: 'Stable', age: 62, diagnosis: 'Post-operative care', date: '2026-05-06', pId: 'P1001' },
            { id: '02', status: 'Occupied', patient: 'Robert Brown', condition: 'Critical', age: 71, diagnosis: 'Cardiac arrest', date: '2026-05-07', pId: 'P1002' },
            { id: '03', status: 'Available', patient: null, diagnosis: '', condition: '', age: '', pId: '' },
            { id: '04', status: 'Available', patient: null, diagnosis: '', condition: '', age: '', pId: '' }
        ],
        discharge() {
            if(confirm('Are you sure you want to discharge this patient?')) {
                this.selectedBed.status = 'Available';
                this.selectedBed.patient = null;
                this.selectedBed.diagnosis = '';
                this.showModal = false;
            }
        },
        startEdit() {
            this.editingPatient = { ...this.selectedBed }; 
            this.isEditing = true;
            this.isAssigning = false;
        },
        saveChanges() {
            Object.assign(this.selectedBed, this.editingPatient);
            this.isEditing = false;
        },
        startAssign() {
            this.editingPatient = { patient: '', diagnosis: '', condition: 'Stable' };
            this.isAssigning = true;
            this.isEditing = false;
        },
        confirmAssignment() {
            if(!this.editingPatient.patient || !this.editingPatient.diagnosis) {
                return alert('Please enter both a name and a diagnosis.');
            }
            this.selectedBed.patient = this.editingPatient.patient;
            this.selectedBed.diagnosis = this.editingPatient.diagnosis;
            this.selectedBed.status = 'Occupied';
            this.showModal = false;
            this.isAssigning = false;
        }
    }">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('wards.index') }}" class="text-blue-600 hover:underline mb-4 inline-block font-bold">← Back to Wards</a>
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $ward->ward_name }} - Bed Management</h2>
                        <p class="text-gray-500 italic">Manage occupancy for {{ $ward->ward_type }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <template x-for="bed in beds" :key="bed.id">
                    <div @click="selectedBed = bed; showModal = true; isEditing = false; isAssigning = false;"
                         class="cursor-pointer bg-white border-2 rounded-2xl p-5 transition-all hover:shadow-lg"
                         :class="bed.status === 'Occupied' ? 'border-blue-100' : 'border-gray-50 bg-gray-50/50'">
                        <div class="flex justify-between items-start mb-4">
                            <span class="font-black text-2xl" :class="bed.status === 'Occupied' ? 'text-blue-900' : 'text-gray-400'">
                                <i class="fas fa-bed mr-1"></i> <span x-text="bed.id"></span>
                            </span>
                            <div class="w-3 h-3 rounded-full" :class="bed.status === 'Occupied' ? 'bg-red-400' : 'bg-green-400'"></div>
                        </div>
                        <div class="min-h-[50px]">
                            <div x-show="bed.patient">
                                <p class="text-sm font-bold text-gray-800" x-text="bed.patient"></p>
                                <p class="text-[11px] text-gray-500" x-text="bed.diagnosis"></p>
                            </div>
                            <div x-show="!bed.patient">
                                <p class="text-xs text-gray-400 italic">No patient assigned</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="showModal" 
             class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
             x-cloak>
            
            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden" @click.away="showModal = false">
                <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold text-gray-800">Bed <span x-text="selectedBed?.id"></span> Details</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <div class="p-8">
                    <div x-show="!isEditing && !isAssigning">
                        <div x-show="selectedBed?.status === 'Occupied'" class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 mb-6">
                            <h4 class="text-blue-800 font-bold mb-4">Patient Information</h4>
                            <div class="space-y-2">
                                <p class="text-sm"><strong>Name:</strong> <span x-text="selectedBed?.patient"></span></p>
                                <p class="text-sm"><strong>Diagnosis:</strong> <span x-text="selectedBed?.diagnosis"></span></p>
                            </div>
                        </div>

                        <div x-show="selectedBed?.status === 'Available'" class="text-center py-6 mb-6">
                            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check text-2xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">This bed is ready for a new patient.</p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div x-show="selectedBed?.status === 'Occupied'" class="flex w-full gap-3">
                                <button @click="startEdit()" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Edit Info</button>
                                <button @click="discharge()" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700">Discharge</button>
                            </div>
                            
                            <button x-show="selectedBed?.status === 'Available'" @click="startAssign()" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold hover:bg-green-700 shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-user-plus"></i> Assign Patient
                            </button>
                        </div>
                    </div>

                    <div x-show="isAssigning" class="space-y-4">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-green-500 rounded-full"></div>
                            <h4 class="font-bold text-gray-800">New Patient Admission</h4>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Full Name</label>
                                <input type="text" x-model="editingPatient.patient" class="w-full rounded-xl border-gray-200 py-3 focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Primary Diagnosis</label>
                                <textarea x-model="editingPatient.diagnosis" rows="3" class="w-full rounded-xl border-gray-200 py-3 focus:ring-2 focus:ring-green-500"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-6">
                            <button @click="isAssigning = false" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-xl font-bold">Cancel</button>
                            <button @click="confirmAssignment()" class="flex-1 bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700">Confirm Admission</button>
                        </div>
                    </div>

                    <div x-show="isEditing" class="space-y-4">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                            <h4 class="font-bold text-gray-800">Update Patient Info</h4>
                        </div>
                        <div class="space-y-4">
                            <input type="text" x-model="editingPatient.patient" class="w-full rounded-xl border-gray-200 py-3 focus:ring-2 focus:ring-blue-500">
                            <textarea x-model="editingPatient.diagnosis" rows="3" class="w-full rounded-xl border-gray-200 py-3 focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex gap-3 pt-6">
                            <button @click="isEditing = false" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-xl font-bold">Cancel</button>
                            <button @click="saveChanges()" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>