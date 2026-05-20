<x-app-layout>
    <div class="bg-gray-100 min-h-screen">
        <!-- Green Top Bar -->
        <div class="bg-green-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-2xl font-bold">WELLMEADOWS HOSPITAL</h1>
                            <p class="text-sm text-green-100">Dashboard | Patient Management</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="flex space-x-4">
                            <a href="{{ route('dashboard') }}" class="bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ route('patients.index') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Patients</a>
                            <a href="#" onclick="showMedicalRecordsView()" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Medical Records</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Appointments</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                            <a href="#" class="text-white hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium">Settings</a>
                        </div>
                        <div class="flex items-center space-x-2 border-l border-green-500 pl-4">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-green-200 hover:text-white ml-2">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Header with Action Buttons -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-wrap gap-3">
                    <button onclick="showView('registration')" id="btnRegistration" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-user-plus"></i> Register Patient
                    </button>
                    <button onclick="showView('patients')" id="btnPatients" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-users"></i> Patients List
                    </button>
                    <button onclick="showView('records')" id="btnRecords" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-notes-medical"></i> Medical Records
                    </button>
                    <button onclick="showView('admissions')" id="btnAdmissions" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-procedures"></i> Admissions & Beds
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Total Patients</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="totalPatients">0</p>
                        </div>
                        <i class="fas fa-users text-3xl text-green-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Active Admissions</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="activeAdmissions">0</p>
                        </div>
                        <i class="fas fa-procedures text-3xl text-blue-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Occupied Beds</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="occupiedBeds">0</p>
                        </div>
                        <i class="fas fa-bed text-3xl text-yellow-300"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Medical Records</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1" id="medicalRecords">0</p>
                        </div>
                        <i class="fas fa-file-medical text-3xl text-purple-300"></i>
                    </div>
                </div>
            </div>

            <!-- VIEW 1: REGISTRATION FORM -->
            <div id="registrationView" class="view-panel">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-user-plus text-green-600 mr-2"></i> Patient Registration Form
                        </h3>
                    </div>
                    <div class="p-6">
                        <form id="registrationForm" onsubmit="registerPatient(event)">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label><input type="text" id="first_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label><input type="text" id="last_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label><input type="date" id="dob" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Gender</label><select id="sex" class="w-full border border-gray-300 rounded-lg px-4 py-2"><option>Male</option><option>Female</option></select></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="text" id="phone" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" id="email" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label><input type="text" id="emergency_name" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Emergency Phone</label><input type="text" id="emergency_phone" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label><select id="blood_type" class="w-full border border-gray-300 rounded-lg px-4 py-2"><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>O+</option><option>O-</option><option>AB+</option><option>AB-</option></select></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Allergies</label><input type="text" id="allergies" class="w-full border border-gray-300 rounded-lg px-4 py-2"></div>
                                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-2">Address</label><textarea id="address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea></div>
                            </div>
                            <div class="mt-6"><button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg"><i class="fas fa-save mr-2"></i> Register Patient</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- VIEW 2: PATIENTS LIST -->
            <div id="patientsView" class="view-panel" style="display: none;">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-users text-green-600 mr-2"></i> Registered Patients List</h3>
                    </div>
                    <div class="p-6">
                        <input type="text" id="searchInput" onkeyup="loadPatients()" placeholder="🔍 Search by name, ID, or phone..." class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Patient ID</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Name</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Age</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Gender</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Phone</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Actions</th></tr></thead>
                                <tbody id="patientsTableBody"><tr><td colspan="6" class="text-center py-4">Loading...</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW 3: MEDICAL RECORDS - COMPLETE DATA FROM patient_medical_record TABLE -->
            <div id="recordsView" class="view-panel" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- LEFT: Medical Records History -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-history text-green-600 mr-2"></i> Medical Records History
                            </h3>
                            <p class="text-sm text-gray-500">Complete medical records from patient_medical_record table</p>
                        </div>
                        <div class="p-4 max-h-[600px] overflow-y-auto" id="recordsList">
                            <div class="text-center py-8 text-gray-500">Loading medical records...</div>
                        </div>
                    </div>

                    <!-- RIGHT: Add New Medical Record Form -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-plus-circle text-green-600 mr-2"></i> Add New Medical Record
                            </h3>
                        </div>
                        <div class="p-6">
                            <form id="medicalRecordForm" onsubmit="saveMedicalRecord(event)">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                                    <select id="recordPatientSelect" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                        <option value="">-- Select Patient --</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                                        <input type="text" id="diagnosis" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                                        <select id="bloodType" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                            <option value="">A+, B-, O+, etc.</option>
                                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                                            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                                    <textarea id="allergiesRecord" rows="2" placeholder="Any allergies?" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Chronic Conditions</label>
                                    <textarea id="chronic_conditions" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prescriptions</label>
                                    <textarea id="prescriptions" rows="2" placeholder="Medications prescribed" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Results</label>
                                    <textarea id="test_results" rows="2" placeholder="Lab results, imaging, etc." class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    <i class="fas fa-save mr-2"></i> Save Medical Record
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW 4: ADMISSIONS & BEDS -->
            <div id="admissionsView" class="view-panel" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold"><i class="fas fa-procedures text-green-600 mr-2"></i> Active Admissions</h3></div>
                        <div class="p-6" id="activeAdmissionsList">Loading...</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold"><i class="fas fa-bed text-green-600 mr-2"></i> Assign Patient to Ward & Bed</h3></div>
                        <div class="p-6">
                            <select id="assignPatientSelect" class="w-full border rounded-lg p-2 mb-3"><option>-- Select Patient --</option></select>
                            <select id="wardSelect" class="w-full border rounded-lg p-2 mb-3" onchange="loadAvailableBeds()"><option>-- Select Ward --</option></select>
                            <select id="bedSelect" class="w-full border rounded-lg p-2 mb-3"><option>-- First select a ward --</option></select>
                            <textarea id="primaryDiagnosis" rows="2" placeholder="Primary diagnosis" class="w-full border rounded-lg p-2 mb-3"></textarea>
                            <button onclick="admitPatient()" class="w-full bg-green-600 text-white py-2 rounded-lg"><i class="fas fa-bed mr-2"></i> Admit Patient</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a54d2cbf95.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const supabaseUrl = '{{ env("SUPABASE_URL") }}';
        const supabaseKey = '{{ env("SUPABASE_KEY") }}';

        function showView(view) {
            document.getElementById('registrationView').style.display = 'none';
            document.getElementById('patientsView').style.display = 'none';
            document.getElementById('recordsView').style.display = 'none';
            document.getElementById('admissionsView').style.display = 'none';
            document.getElementById(`${view}View`).style.display = 'block';
            
            const btnClass = 'bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition';
            const activeClass = 'bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition';
            document.getElementById('btnRegistration').className = view === 'registration' ? activeClass : btnClass;
            document.getElementById('btnPatients').className = view === 'patients' ? activeClass : btnClass;
            document.getElementById('btnRecords').className = view === 'records' ? activeClass : btnClass;
            document.getElementById('btnAdmissions').className = view === 'admissions' ? activeClass : btnClass;
            
            if (view === 'patients') loadPatients();
            if (view === 'records') { loadCompleteMedicalRecords(); loadPatientSelects(); }
            if (view === 'admissions') { loadActiveAdmissions(); loadPatientSelects(); }
        }

        // FETCH COMPLETE MEDICAL RECORDS FROM patient_medical_record TABLE
        async function loadCompleteMedicalRecords() {
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=*,patient(patient_id,first_name,last_name,date_registered)`, {
                    method: 'GET',
                    headers: {
                        'apikey': supabaseKey,
                        'Authorization': `Bearer ${supabaseKey}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                const records = await response.json();
                const container = document.getElementById('recordsList');
                
                if (!records || records.length === 0) {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500">No medical records found. Add your first record!</div>';
                    return;
                }
                
                // Display COMPLETE medical record data
                container.innerHTML = records.map(record => {
                    const patient = record.patient || {};
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg">${patient.first_name || 'Unknown'} ${patient.last_name || 'Patient'}</h4>
                                    <p class="text-xs text-gray-500">ID: ${patient.patient_id || 'N/A'}</p>
                                </div>
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Record #${record.id}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="space-y-1">
                                    <p><span class="font-semibold">🩸 Blood Type:</span> ${record.blood_type || 'Not recorded'}</p>
                                    <p><span class="font-semibold">⚠️ Allergies:</span> ${record.allergies || 'None'}</p>
                                    <p><span class="font-semibold">📋 Diagnosis:</span> ${record.diagnosis || 'Not recorded'}</p>
                                </div>
                                <div class="space-y-1">
                                    <p><span class="font-semibold">🏥 Chronic Conditions:</span> ${record.chronic_conditions || 'None'}</p>
                                    <p><span class="font-semibold">💊 Prescriptions:</span> ${record.prescriptions || 'None'}</p>
                                    <p><span class="font-semibold">🔬 Test Results:</span> ${record.test_results || 'None'}</p>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t text-right">
                                <button onclick="deleteMedicalRecord(${record.id})" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Update medical records count
                document.getElementById('medicalRecords').textContent = records.length;
                
            } catch (error) {
                console.error('Error loading medical records:', error);
                document.getElementById('recordsList').innerHTML = '<div class="text-center py-8 text-red-500">Error loading medical records</div>';
            }
        }

        // Save new medical record to patient_medical_record table
        async function saveMedicalRecord(event) {
            event.preventDefault();
            
            const patientId = document.getElementById('recordPatientSelect').value;
            if (!patientId) {
                alert('Please select a patient');
                return;
            }
            
            const formData = {
                patient_id: parseInt(patientId),
                diagnosis: document.getElementById('diagnosis').value,
                blood_type: document.getElementById('bloodType').value,
                allergies: document.getElementById('allergiesRecord').value,
                chronic_conditions: document.getElementById('chronic_conditions').value,
                prescriptions: document.getElementById('prescriptions').value,
                test_results: document.getElementById('test_results').value,
                created_at: new Date().toISOString()
            };
            
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record`, {
                    method: 'POST',
                    headers: {
                        'apikey': supabaseKey,
                        'Authorization': `Bearer ${supabaseKey}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                if (response.ok) {
                    alert('✅ Medical record saved successfully!');
                    document.getElementById('medicalRecordForm').reset();
                    loadCompleteMedicalRecords();
                    fetchStats();
                } else {
                    const error = await response.text();
                    alert('❌ Error: ' + error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error saving medical record');
            }
        }

        // Delete medical record
        async function deleteMedicalRecord(recordId) {
            if (!confirm('Are you sure you want to delete this medical record?')) return;
            
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?id=eq.${recordId}`, {
                    method: 'DELETE',
                    headers: {
                        'apikey': supabaseKey,
                        'Authorization': `Bearer ${supabaseKey}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    alert('✅ Medical record deleted');
                    loadCompleteMedicalRecords();
                    fetchStats();
                } else {
                    alert('❌ Error deleting record');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error deleting record');
            }
        }

        async function fetchStats() {
            try {
                const response = await fetch('/stats');
                const stats = await response.json();
                document.getElementById('totalPatients').textContent = stats.total_patients || 0;
                document.getElementById('activeAdmissions').textContent = stats.active_admissions || 0;
                document.getElementById('occupiedBeds').textContent = stats.occupied_beds || 0;
                document.getElementById('medicalRecords').textContent = stats.medical_records || 0;
            } catch (error) { console.error('Error:', error); }
        }

        async function loadPatients() {
            try {
                const response = await fetch('/patients/list');
                const patients = await response.json();
                const tbody = document.getElementById('patientsTableBody');
                if (!patients || patients.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No patients found</td></tr>'; return; }
                tbody.innerHTML = patients.map(p => `<tr><td class="px-6 py-4">${p.patient_id}</td><td class="px-6 py-4">${p.first_name} ${p.last_name}</td><td class="px-6 py-4">${p.dob ? Math.floor((new Date() - new Date(p.dob)) / 31536000000) : 'N/A'}</td><td class="px-6 py-4">${p.sex || '—'}</td><td class="px-6 py-4">${p.phone || '—'}</td><td class="px-6 py-4"><button class="text-green-600 mr-2" onclick="viewPatient(${p.patient_id})">View</button><button class="text-red-600" onclick="deletePatient(${p.patient_id})">Delete</button></td></tr>`).join('');
            } catch (error) { console.error('Error:', error); }
        }

        async function loadPatientSelects() {
            try {
                const response = await fetch('/patients/list');
                const patients = await response.json();
                const options = '<option value="">-- Select Patient --</option>' + patients.map(p => `<option value="${p.patient_id}">${p.first_name} ${p.last_name} (ID: ${p.patient_id})</option>`).join('');
                document.getElementById('recordPatientSelect').innerHTML = options;
                document.getElementById('assignPatientSelect').innerHTML = options;
            } catch (error) { console.error('Error:', error); }
        }

        async function loadActiveAdmissions() {
            try {
                const response = await fetch('/admissions/active');
                const admissions = await response.json();
                const container = document.getElementById('activeAdmissionsList');
                if (!admissions || admissions.length === 0) { container.innerHTML = '<div class="text-center py-4">No active admissions</div>'; return; }
                container.innerHTML = admissions.map(adm => `<div class="border p-3 mb-2 rounded"><strong>${adm.patient?.first_name} ${adm.patient?.last_name}</strong><br>Bed: ${adm.bed?.bed_number} | Admitted: ${new Date(adm.date_admitted).toLocaleDateString()}<br><button onclick="dischargePatient(${adm.inpatient_id})" class="text-red-600 text-sm mt-2">Discharge</button></div>`).join('');
            } catch (error) { console.error('Error:', error); }
        }

        async function loadAvailableBeds() {
            const wardId = document.getElementById('wardSelect').value;
            if (!wardId) return;
            try {
                const response = await fetch(`/beds/available/${wardId}`);
                const beds = await response.json();
                document.getElementById('bedSelect').innerHTML = '<option value="">-- Select Bed --</option>' + beds.map(b => `<option value="${b.bed_id}">Bed ${b.bed_number}</option>`).join('');
            } catch (error) { console.error('Error:', error); }
        }

        async function admitPatient() {
            const patientId = document.getElementById('assignPatientSelect').value;
            const bedId = document.getElementById('bedSelect').value;
            if (!patientId || !bedId) { alert('Please select patient and bed'); return; }
            try {
                const response = await fetch('/admissions', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ patient_id: parseInt(patientId), bed_id: parseInt(bedId), primary_diagnosis: document.getElementById('primaryDiagnosis').value }) });
                const result = await response.json();
                if (result.success) { alert('✅ Patient admitted'); fetchStats(); loadActiveAdmissions(); } else { alert('❌ Error'); }
            } catch (error) { alert('Error'); }
        }

        function viewPatient(id) { alert('View patient ' + id); }
        async function deletePatient(id) { if(confirm('Delete?')){ await fetch(`/patients/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } }); loadPatients(); fetchStats(); } }
        async function dischargePatient(id) { if(confirm('Discharge?')){ await fetch(`/admissions/${id}/discharge`, { method: 'PUT', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }, body: JSON.stringify({ discharge_date: new Date().toISOString().split('T')[0] }) }); loadActiveAdmissions(); fetchStats(); } }
        async function registerPatient(event) { event.preventDefault(); alert('Patient registration feature - implement with your backend'); }

        document.addEventListener('DOMContentLoaded', function() {
            fetchStats();
            loadPatients();
            loadPatientSelects();
            loadActiveAdmissions();
            showView('records'); // Show medical records view by default
        });
    </script>
</x-app-layout>