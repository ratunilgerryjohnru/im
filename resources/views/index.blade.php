<x-app-layout>
    <div class="py-12 bg-gray-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 inline-block bg-gray-300 px-6 py-2 shadow-sm">
                <h1 class="text-xl font-bold text-gray-700">Module 1</h1>
            </div>

            <div class="bg-white shadow-lg overflow-hidden rounded-lg">
                <div class="bg-green-500 p-6">
                    <h2 class="text-2xl text-white">Patient Management System</h2>
                </div>

                <div class="p-8 bg-gray-100 space-y-8">
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Total Patients</p>
                            <p class="text-2xl font-bold text-green-600" id="totalPatients">0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Active Admissions</p>
                            <p class="text-2xl font-bold text-green-600" id="activeAdmissions">0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Occupied Beds</p>
                            <p class="text-2xl font-bold text-green-600" id="occupiedBeds">0</p>
                        </div>
                        <div class="bg-white p-4 text-center shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-600">Medical Records</p>
                            <p class="text-2xl font-bold text-green-600" id="medicalRecords">0</p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex space-x-2 border-b border-gray-300 pb-4">
                        <button onclick="showView('registration')" id="btnRegistration" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                            📋 Register Patient
                        </button>
                        <button onclick="showView('patients')" id="btnPatients" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                            👥 Registered Patients
                        </button>
                        <button onclick="showView('records')" id="btnRecords" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                            📄 Medical Records
                        </button>
                    </div>

                    <!-- VIEW 1: REGISTRATION FORM -->
                    <div id="registrationView" class="view-panel">
                        <div class="bg-white p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-gray-700 mb-4 border-b pb-2 font-bold">➕ Patient Registration Form</h3>
                            <form id="registrationForm" onsubmit="registerPatient(event)">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">First Name *</label>
                                        <input type="text" id="first_name" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Last Name *</label>
                                        <input type="text" id="last_name" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Date of Birth *</label>
                                        <input type="date" id="dob" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Gender *</label>
                                        <select id="gender" required class="w-full border border-gray-400 p-2 rounded">
                                            <option value="">Select</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Phone *</label>
                                        <input type="text" id="phone" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Email</label>
                                        <input type="email" id="email" class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Emergency Contact Name *</label>
                                        <input type="text" id="emergency_name" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Emergency Phone *</label>
                                        <input type="text" id="emergency_phone" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Blood Group *</label>
                                        <select id="blood_group" required class="w-full border border-gray-400 p-2 rounded">
                                            <option value="">Select</option>
                                            <option>A+</option>
                                            <option>A-</option>
                                            <option>B+</option>
                                            <option>B-</option>
                                            <option>O+</option>
                                            <option>O-</option>
                                            <option>AB+</option>
                                            <option>AB-</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Allergies *</label>
                                        <input type="text" id="allergies" required class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-xs mb-1 font-semibold">Address *</label>
                                        <textarea id="address" rows="2" required class="w-full border border-gray-400 p-2 rounded"></textarea>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition">
                                            Register Patient
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- VIEW 2: REGISTERED PATIENTS LIST -->
                    <div id="patientsView" class="view-panel" style="display: none;">
                        <div class="bg-white p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-green-600 font-bold mb-4">👥 Registered Patients</h3>
                            <input type="text" id="searchInput" onkeyup="loadPatients()" placeholder="Search by name, ID, or phone..." class="w-full border border-gray-300 p-2 mb-4 rounded">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b-2 border-gray-800 bg-gray-50">
                                            <th class="py-2 px-2 text-sm">Patient ID</th>
                                            <th class="py-2 px-2 text-sm">Name</th>
                                            <th class="py-2 px-2 text-sm">Age</th>
                                            <th class="py-2 px-2 text-sm">Gender</th>
                                            <th class="py-2 px-2 text-sm">Phone</th>
                                            <th class="py-2 px-2 text-sm">Blood Group</th>
                                            <th class="py-2 px-2 text-sm">Actions</th>
                                        </table>
                                    </thead>
                                    <tbody id="patientsTableBody">
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-gray-500">No patients registered yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 3: MEDICAL RECORDS -->
                    <div id="recordsView" class="view-panel" style="display: none;">
                        <div class="bg-white p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-green-600 font-bold mb-4">📑 Medical Records</h3>
                            
                            <!-- Records List -->
                            <div id="recordsList" class="mb-6 space-y-3 max-h-96 overflow-y-auto">
                                <div class="text-center text-gray-500 py-4">No medical records added yet.</div>
                            </div>

                            <!-- Add New Record Form -->
                            <div class="border-t-2 border-gray-200 pt-4 mt-4">
                                <h4 class="font-bold text-gray-700 mb-3">➕ Add New Medical Record</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Patient *</label>
                                        <select id="recordPatientSelect" class="w-full border border-gray-400 p-2 rounded">
                                            <option value="">-- Select Patient --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Record Date *</label>
                                        <input type="date" id="recordDate" class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Record Type *</label>
                                        <select id="recordType" class="w-full border border-gray-400 p-2 rounded">
                                            <option value="">-- Select Type --</option>
                                            <option value="diagnosis">🏥 Diagnosis</option>
                                            <option value="prescription">💊 Prescription</option>
                                            <option value="treatment">🩺 Treatment</option>
                                            <option value="lab_result">🔬 Lab Result</option>
                                            <option value="note">📝 General Note</option>
                                            <option value="follow_up">📅 Follow-up</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs mb-1 font-semibold">Recorded By (Doctor/Staff) *</label>
                                        <input type="text" id="recordedBy" placeholder="Dr. Smith or Nurse Johnson" class="w-full border border-gray-400 p-2 rounded">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs mb-1 font-semibold">Description / Details *</label>
                                        <textarea id="recordDescription" rows="4" placeholder="Enter detailed medical record information here..." class="w-full border border-gray-400 p-2 rounded"></textarea>
                                    </div>
                                </div>
                                <button onclick="addMedicalRecord()" class="mt-4 bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">
                                    Save Medical Record
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .view-panel {
            transition: all 0.3s ease;
        }
        .action-btn {
            background: #e5e7eb;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin: 0 2px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: #d1d5db;
            transform: translateY(-1px);
        }
        .delete-btn {
            background: #fee2e2;
            color: #991b1b;
        }
        .delete-btn:hover {
            background: #fecaca;
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        // Fetch statistics
        async function fetchStats() {
            try {
                const response = await fetch('/stats');
                const stats = await response.json();
                document.getElementById('totalPatients').textContent = stats.total_patients || 0;
                document.getElementById('activeAdmissions').textContent = stats.active_admissions || 0;
                document.getElementById('occupiedBeds').textContent = stats.occupied_beds || 0;
                document.getElementById('medicalRecords').textContent = stats.medical_records || 0;
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        }

        // Register Patient function
        async function registerPatient(event) {
            event.preventDefault();
            
            const formData = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                emergency_name: document.getElementById('emergency_name').value,
                emergency_phone: document.getElementById('emergency_phone').value,
                blood_group: document.getElementById('blood_group').value,
                allergies: document.getElementById('allergies').value,
                address: document.getElementById('address').value,
                _token: csrfToken
            };
            
            // Validate required fields
            if (!formData.first_name || !formData.last_name || !formData.dob || !formData.gender || !formData.phone || !formData.emergency_name || !formData.emergency_phone || !formData.blood_group || !formData.allergies || !formData.address) {
                alert('Please fill all required fields');
                return;
            }
            
            try {
                const response = await fetch('/patients', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ ' + result.message);
                    document.getElementById('registrationForm').reset();
                    fetchStats();
                    loadPatients();
                    loadPatientSelect();
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error registering patient. Please check the console for details.');
            }
        }

        // Load patients list with search
        async function loadPatients() {
            try {
                const search = document.getElementById('searchInput')?.value || '';
                const response = await fetch(`/patients/list?search=${encodeURIComponent(search)}`);
                const patients = await response.json();
                
                const tbody = document.getElementById('patientsTableBody');
                if (patients.length === 0) {
                    tbody.innerHTML = '</tr><td colspan="7" class="py-4 text-center text-gray-500">No patients found.</td></tr>';
                    return;
                }
                
                tbody.innerHTML = patients.map(p => {
                    const fullName = `${p.first_name} ${p.last_name}`;
                    const age = p.dob ? Math.floor((new Date() - new Date(p.dob)) / (365.25 * 24 * 60 * 60 * 1000)) : 'N/A';
                    return `<tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-2 text-sm">${p.patient_id}</td>
                        <td class="py-2 px-2 text-sm font-semibold">${fullName}</td>
                        <td class="py-2 px-2 text-sm">${age}</td>
                        <td class="py-2 px-2 text-sm">${p.gender || '—'}</td>
                        <td class="py-2 px-2 text-sm">${p.phone || '—'}</td>
                        <td class="py-2 px-2 text-sm">${p.blood_group || '—'}</td>
                        <td class="py-2 px-2 text-sm">
                            <button onclick="toggleAdmission(${p.id})" class="action-btn">
                                ${p.admission_status ? 'Discharge' : 'Admit'}
                            </button>
                            <button onclick="toggleBed(${p.id})" class="action-btn">
                                ${p.bed_occupied ? 'Free Bed' : 'Assign Bed'}
                            </button>
                            <button onclick="deletePatient(${p.id})" class="action-btn delete-btn">
                                Delete
                            </button>
                        </td>
                    </tr>`;
                }).join('');
            } catch (error) {
                console.error('Error loading patients:', error);
            }
        }

        // Toggle admission status
        async function toggleAdmission(id) {
            try {
                await fetch(`/patients/${id}/toggle-admission`, { 
                    method: 'PUT', 
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken 
                    } 
                });
                fetchStats();
                loadPatients();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Toggle bed occupancy
        async function toggleBed(id) {
            try {
                await fetch(`/patients/${id}/toggle-bed`, { 
                    method: 'PUT', 
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken 
                    } 
                });
                fetchStats();
                loadPatients();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Delete patient
        async function deletePatient(id) {
            if (confirm('⚠️ Delete this patient and all associated medical records? This action cannot be undone.')) {
                try {
                    await fetch(`/patients/${id}`, { 
                        method: 'DELETE', 
                        headers: { 'X-CSRF-TOKEN': csrfToken } 
                    });
                    fetchStats();
                    loadPatients();
                    loadMedicalRecords();
                    loadPatientSelect();
                    alert('✅ Patient deleted successfully');
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error deleting patient');
                }
            }
        }

        // Load medical records
        async function loadMedicalRecords() {
            try {
                const response = await fetch('/medical-records');
                const records = await response.json();
                const container = document.getElementById('recordsList');
                
                if (records.length === 0) {
                    container.innerHTML = '<div class="text-center text-gray-500 py-4">📭 No medical records yet. Add one above.</div>';
                    return;
                }
                
                const recordTypeIcons = {
                    'diagnosis': '🏥',
                    'prescription': '💊',
                    'treatment': '🩺',
                    'lab_result': '🔬',
                    'note': '📝',
                    'follow_up': '📅'
                };
                
                container.innerHTML = records.map(record => {
                    const patientName = record.patient ? `${record.patient.first_name} ${record.patient.last_name}` : 'Unknown Patient';
                    const patientId = record.patient ? record.patient.patient_id : 'N/A';
                    const icon = recordTypeIcons[record.record_type] || '📋';
                    
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">${icon}</span>
                                    <div>
                                        <div class="font-bold text-gray-800">${patientName}</div>
                                        <div class="text-xs text-gray-500">ID: ${patientId}</div>
                                    </div>
                                </div>
                                <button onclick="deleteRecord(${record.record_id})" class="text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded hover:bg-red-50">
                                    🗑️ Delete
                                </button>
                            </div>
                            <div class="ml-8">
                                <div class="flex flex-wrap gap-3 text-sm mb-2">
                                    <span class="font-semibold text-blue-600">📅 ${record.record_date}</span>
                                    <span class="text-gray-500">👨‍⚕️ ${record.recorded_by || 'Unknown'}</span>
                                </div>
                                <div class="text-gray-700 mt-2 p-2 bg-white rounded border border-gray-100">
                                    ${record.description}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error loading records:', error);
                document.getElementById('recordsList').innerHTML = '<div class="text-center text-red-500 py-4">Error loading medical records</div>';
            }
        }

        // Add medical record
        async function addMedicalRecord() {
            const patientId = document.getElementById('recordPatientSelect').value;
            const recordDate = document.getElementById('recordDate').value;
            const recordType = document.getElementById('recordType').value;
            const recordedBy = document.getElementById('recordedBy').value;
            const description = document.getElementById('recordDescription').value;
            
            if (!patientId) {
                alert('Please select a patient');
                return;
            }
            if (!recordDate) {
                alert('Please select a record date');
                return;
            }
            if (!recordType) {
                alert('Please select a record type');
                return;
            }
            if (!recordedBy) {
                alert('Please enter the doctor/staff name');
                return;
            }
            if (!description.trim()) {
                alert('Please enter the description/details');
                return;
            }
            
            const data = {
                patient_id: patientId,
                record_date: recordDate,
                record_type: recordType,
                recorded_by: recordedBy,
                description: description,
                _token: csrfToken
            };
            
            try {
                const response = await fetch('/medical-records', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Medical record added successfully!');
                    // Reset form
                    document.getElementById('recordDate').value = new Date().toISOString().split('T')[0];
                    document.getElementById('recordType').value = '';
                    document.getElementById('recordedBy').value = '';
                    document.getElementById('recordDescription').value = '';
                    
                    // Refresh data
                    fetchStats();
                    loadMedicalRecords();
                } else {
                    alert('❌ Error: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error adding medical record');
            }
        }

        // Delete medical record
        async function deleteRecord(recordId) {
            if (!confirm('⚠️ Delete this medical record? This action cannot be undone.')) {
                return;
            }
            
            try {
                const response = await fetch(`/medical-records/${recordId}`, {
                    method: 'DELETE',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Medical record deleted successfully');
                    fetchStats();
                    loadMedicalRecords();
                } else {
                    alert('❌ Error deleting record');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error deleting medical record');
            }
        }

        // Load patient dropdown for medical records
        async function loadPatientSelect() {
            try {
                const response = await fetch('/patients/list');
                const patients = await response.json();
                const select = document.getElementById('recordPatientSelect');
                select.innerHTML = '<option value="">-- Select Patient --</option>' + 
                    patients.map(p => `<option value="${p.id}">${p.first_name} ${p.last_name} (${p.patient_id})</option>`).join('');
            } catch (error) {
                console.error('Error loading patients:', error);
            }
        }

        // View navigation
        function showView(view) {
            // Hide all views
            document.getElementById('registrationView').style.display = 'none';
            document.getElementById('patientsView').style.display = 'none';
            document.getElementById('recordsView').style.display = 'none';
            
            // Show selected view
            document.getElementById(`${view}View`).style.display = 'block';
            
            // Update button styles
            const btnClasses = (active) => active ? 'bg-green-500 text-white px-6 py-2 rounded' : 'bg-gray-300 text-gray-700 px-6 py-2 rounded';
            document.getElementById('btnRegistration').className = btnClasses(view === 'registration');
            document.getElementById('btnPatients').className = btnClasses(view === 'patients');
            document.getElementById('btnRecords').className = btnClasses(view === 'records');
            
            // Refresh data when switching views
            if (view === 'patients') {
                loadPatients();
            } else if (view === 'records') { 
                loadMedicalRecords(); 
                loadPatientSelect();
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date as default for medical record date
            const today = new Date().toISOString().split('T')[0];
            if (document.getElementById('recordDate')) {
                document.getElementById('recordDate').value = today;
            }
            
            // Load initial data
            fetchStats();
            loadPatients();
            loadMedicalRecords();
            loadPatientSelect();
        });
    </script>
</x-app-layout>