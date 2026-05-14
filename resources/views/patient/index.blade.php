@extends('layouts.app')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-value" id="totalPatients">0</div>
        <div class="stat-label">TOTAL PATIENTS</div>
    </div>
    <div class="stat-card admissions">
        <div class="stat-value" id="activeAdmissions">0</div>
        <div class="stat-label">ACTIVE ADMISSIONS</div>
    </div>
    <div class="stat-card beds">
        <div class="stat-value" id="occupiedBeds">0</div>
        <div class="stat-label">OCCUPIED BEDS</div>
    </div>
    <div class="stat-card records">
        <div class="stat-value" id="medicalRecords">0</div>
        <div class="stat-label">MEDICAL RECORDS</div>
    </div>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <button onclick="showView('registration')" id="btnRegistration" class="btn-primary-custom">
        <i class="fas fa-user-plus"></i> Register Patient
    </button>
    <button onclick="showView('patients')" id="btnPatients" class="btn-secondary-custom">
        <i class="fas fa-users"></i> Patients List
    </button>
    <button onclick="showView('records')" id="btnRecords" class="btn-secondary-custom">
        <i class="fas fa-notes-medical"></i> Medical Records
    </button>
    <button onclick="showView('admissions')" id="btnAdmissions" class="btn-secondary-custom">
        <i class="fas fa-procedures"></i> Admissions & Beds
    </button>
</div>

<!-- VIEW 1: REGISTRATION FORM -->
<div id="registrationView" class="view-panel">
    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-user-plus"></i> Patient Registration Form
        </div>
        <div class="card-body">
            <form id="registrationForm" onsubmit="registerPatient(event)">
                @csrf
                <div class="form-grid">
                    <div><label class="form-label">First Name *</label><input type="text" id="first_name" required class="form-input"></div>
                    <div><label class="form-label">Last Name *</label><input type="text" id="last_name" required class="form-input"></div>
                    <div><label class="form-label">Date of Birth</label><input type="date" id="dob" class="form-input"></div>
                    <div><label class="form-label">Gender</label><select id="sex" class="form-input"><option value="">Select</option><option>Male</option><option>Female</option></select></div>
                    <div><label class="form-label">Phone</label><input type="text" id="phone" class="form-input"></div>
                    <div><label class="form-label">Email</label><input type="email" id="email" class="form-input"></div>
                    <div><label class="form-label">Emergency Contact</label><input type="text" id="emergency_name" class="form-input"></div>
                    <div><label class="form-label">Emergency Phone</label><input type="text" id="emergency_phone" class="form-input"></div>
                    <div><label class="form-label">Blood Group</label><select id="blood_type" class="form-input"><option value="">Select</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>O+</option><option>O-</option><option>AB+</option><option>AB-</option></select></div>
                    <div><label class="form-label">Allergies</label><input type="text" id="allergies" class="form-input"></div>
                    <div class="full-width"><label class="form-label">Address</label><textarea id="address" rows="2" class="form-input"></textarea></div>
                </div>
                <div class="mt-4"><button type="submit" class="btn-primary-custom"><i class="fas fa-save"></i> Register Patient</button></div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW 2: PATIENTS LIST -->
<div id="patientsView" class="view-panel" style="display: none;">
    <div class="table-card">
        <div class="card-header"><i class="fas fa-users"></i> Registered Patients List</div>
        <div class="card-body">
            <input type="text" id="searchInput" onkeyup="loadPatients()" placeholder="🔍 Search by name, ID, or phone..." class="form-input mb-4">
            <div class="overflow-x-auto">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Marital Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-8">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- VIEW 3: MEDICAL RECORDS -->
<div id="recordsView" class="view-panel" style="display: none;">
    <div class="grid-2">
        <div class="table-card">
            <div class="card-header"><i class="fas fa-history"></i> Medical Records History</div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="recordsList"><div class="text-center py-8">Loading...</div></div>
        </div>
        <div class="form-card">
            <div class="card-header"><i class="fas fa-plus-circle"></i> Add New Medical Record</div>
            <div class="card-body">
                <div><label class="form-label">Patient *</label><select id="recordPatientSelect" class="form-input"><option value="">-- Select Patient --</option></select></div>
                <div class="form-grid" style="grid-template-columns: repeat(2, 1fr); margin-top: 12px;">
                    <div><label class="form-label">Diagnosis</label><input type="text" id="diagnosis" class="form-input"></div>
                    <div><label class="form-label">Blood Type</label><input type="text" id="bloodType" class="form-input" placeholder="A+, B-, O+, etc."></div>
                </div>
                <div style="margin-top: 12px;"><label class="form-label">Allergies</label><textarea id="allergiesRecord" rows="3" class="form-input" placeholder="Any allergies?"></textarea></div>
                <div style="margin-top: 12px;"><label class="form-label">Chronic Conditions</label><textarea id="chronic_conditions" rows="3" class="form-input"></textarea></div>
                <button onclick="addMedicalRecord()" class="btn-primary-custom w-100" style="margin-top: 16px;"><i class="fas fa-save"></i> Save Medical Record</button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW 4: ADMISSIONS & BEDS -->
<div id="admissionsView" class="view-panel" style="display: none;">
    <div class="grid-2">
        <div class="table-card">
            <div class="card-header"><i class="fas fa-procedures"></i> Active Admissions</div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="activeAdmissionsList">
                <div class="text-center py-8">Loading...</div>
            </div>
        </div>
        <div class="form-card">
            <div class="card-header"><i class="fas fa-bed"></i> Assign Patient to Ward & Bed</div>
            <div class="card-body">
                <div><label class="form-label">Select Patient</label><select id="assignPatientSelect" class="form-input"><option value="">-- Select Patient --</option></select></div>
                <div class="mt-4"><label class="form-label">Select Ward</label><select id="wardSelect" class="form-input" onchange="loadAvailableBeds()">
                    <option value="">-- Select Ward --</option>
                    <option value="1">Ward 1 (General Ward)</option>
                    <option value="2">Ward 2 (Maternity)</option>
                    <option value="3">Ward 3 (Pediatric)</option>
                    <option value="4">Ward 4 (ICU)</option>
                </select></div>
                <div class="mt-4"><label class="form-label">Select Bed</label><select id="bedSelect" class="form-input"><option value="">-- First select a ward --</option></select></div>
                <div class="mt-4"><label class="form-label">Primary Diagnosis</label><textarea id="primaryDiagnosis" rows="2" class="form-input" placeholder="Enter primary diagnosis"></textarea></div>
                <div class="mt-4"><button onclick="admitPatient()" class="btn-primary-custom w-100"><i class="fas fa-bed"></i> Admit Patient</button></div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const supabaseUrl = '{{ env("SUPABASE_URL") }}';
    const supabaseKey = '{{ env("SUPABASE_KEY") }}';

    // Fetch all dashboard stats directly from Supabase
    async function fetchStats() {
        try {
            // Get total patients
            const patientsRes = await fetch(`${supabaseUrl}/rest/v1/patient?select=patient_id`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const patients = await patientsRes.json();
            document.getElementById('totalPatients').textContent = patients?.length || 0;

            // Get active admissions (in_patient with actual_leave = null)
            const admissionsRes = await fetch(`${supabaseUrl}/rest/v1/in_patient?actual_leave=is.null&select=*`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const admissions = await admissionsRes.json();
            document.getElementById('activeAdmissions').textContent = admissions?.length || 0;

            // Get occupied beds (is_available = false)
            const bedsRes = await fetch(`${supabaseUrl}/rest/v1/bed?is_available=eq.false&select=*`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const beds = await bedsRes.json();
            document.getElementById('occupiedBeds').textContent = beds?.length || 0;

            // Get medical records
            const recordsRes = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=*`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const records = await recordsRes.json();
            document.getElementById('medicalRecords').textContent = records?.length || 0;

        } catch (error) { 
            console.error('Error fetching stats:', error);
        }
    }

    // Register a new patient
    async function registerPatient(event) {
        event.preventDefault();
        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            dob: document.getElementById('dob').value,
            sex: document.getElementById('sex').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            emergency_name: document.getElementById('emergency_name').value,
            emergency_phone: document.getElementById('emergency_phone').value,
            blood_group: document.getElementById('blood_type').value,
            allergies: document.getElementById('allergies').value,
            address: document.getElementById('address').value,
            _token: csrfToken
        };
        
        try {
            const response = await fetch('/patients', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(formData)
            });
            const result = await response.json();
            if (result.success) {
                alert('✅ Patient registered successfully!');
                document.getElementById('registrationForm').reset();
                fetchStats(); 
                loadPatients(); 
                loadPatientSelects();
            } else { 
                alert('❌ Error: ' + (result.message || 'Registration failed')); 
            }
        } catch (error) { 
            console.error('Error:', error);
            alert('❌ Error registering patient'); 
        }
    }

    // Load patients list
    async function loadPatients() {
        try {
            const search = document.getElementById('searchInput')?.value || '';
            const response = await fetch(`/patients/list?search=${encodeURIComponent(search)}`);
            const patients = await response.json();
            const tbody = document.getElementById('patientsTableBody');
            
            if (!patients || patients.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8">No patients found.</td></tr>'; 
                return; 
            }
            
            tbody.innerHTML = patients.map(p => {
                const age = p.dob ? Math.floor((new Date() - new Date(p.dob)) / (365.25 * 24 * 60 * 60 * 1000)) : 'N/A';
                return `
                    <tr>
                        <td>${p.patient_id}</td>
                        <td><strong>${p.first_name} ${p.last_name}</strong></td>
                        <td>${age}</td>
                        <td>${p.sex || '—'}</td>
                        <td>${p.phone || '—'}</td>
                        <td>${p.marital_status || '—'}</td>
                        <td>
                            <button onclick="viewPatient(${p.patient_id})" class="table-action-btn">View</button>
                            <button onclick="deletePatient(${p.patient_id})" class="table-action-btn danger">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) { 
            console.error('Error loading patients:', error);
            document.getElementById('patientsTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-danger">Error loading patients</td></tr>';
        }
    }

    function viewPatient(patientId) {
        alert('View patient ' + patientId);
    }

    async function deletePatient(patientId) {
        if (!confirm('⚠️ Delete this patient?')) return;
        
        try {
            const response = await fetch(`/patients/${patientId}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                alert('✅ Patient deleted');
                fetchStats(); 
                loadPatients(); 
                loadPatientSelects();
            } else {
                alert('❌ Error: ' + (result.message || 'Delete failed'));
            }
        } catch (error) { 
            alert('❌ Error deleting patient');
        }
    }

    async function loadMedicalRecords() {
        try {
            const response = await fetch('/patients/list');
            const patients = await response.json();
            const container = document.getElementById('recordsList');
            
            if (!patients || patients.length === 0) { 
                container.innerHTML = '<div class="text-center py-8">No patients found.</div>'; 
                return; 
            }
            
            // Get actual medical records from Supabase
            const recordsRes = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=*`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const medicalRecords = await recordsRes.json();
            
            // Create a map of patient_id to medical record
            const recordMap = {};
            medicalRecords.forEach(record => {
                recordMap[record.patient_id] = record;
            });
            
            container.innerHTML = patients.slice(0, 20).map(patient => {
                const record = recordMap[patient.patient_id];
                return `
                    <div class="record-card">
                        <div style="display: flex; justify-content: space-between;">
                            <strong>${patient.first_name} ${patient.last_name}</strong>
                            <span style="font-size: 0.7rem; color: #6b7280;">ID: ${patient.patient_id}</span>
                        </div>
                        <div style="font-size: 0.8rem; margin-top: 8px;">
                            <div>🩸 Blood Type: ${record?.blood_type || 'Not recorded'}</div>
                            <div>⚠️ Allergies: ${record?.allergies || 'None'}</div>
                            <div>📅 Registered: ${patient.date_registered || 'N/A'}</div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (error) { 
            console.error('Error:', error);
            document.getElementById('recordsList').innerHTML = '<div class="text-center py-8">Error loading records</div>';
        }
    }

    async function addMedicalRecord() {
        const patientId = document.getElementById('recordPatientSelect').value;
        if (!patientId) { 
            alert('Please select a patient'); 
            return; 
        }
        
        alert(`✅ Medical record feature coming soon. Patient ID: ${patientId}`);
        
        document.getElementById('diagnosis').value = '';
        document.getElementById('bloodType').value = '';
        document.getElementById('allergiesRecord').value = '';
        document.getElementById('chronic_conditions').value = '';
    }

    async function loadPatientSelects() {
        try {
            const response = await fetch('/patients/list');
            const patients = await response.json();
            const options = '<option value="">-- Select Patient --</option>' + patients.map(p => `<option value="${p.patient_id}">${p.first_name} ${p.last_name} (ID: ${p.patient_id})</option>`).join('');
            document.getElementById('recordPatientSelect').innerHTML = options;
            document.getElementById('assignPatientSelect').innerHTML = options;
        } catch (error) { 
            console.error('Error:', error);
        }
    }

    async function loadAvailableBeds() {
        const wardId = document.getElementById('wardSelect').value;
        if (!wardId) { 
            document.getElementById('bedSelect').innerHTML = '<option value="">-- First select a ward --</option>'; 
            return; 
        }
        
        try {
            // Get available beds from Supabase directly
            const response = await fetch(`${supabaseUrl}/rest/v1/bed?ward_id=eq.${wardId}&is_available=eq.true&select=bed_id,bed_number,bed_type`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const beds = await response.json();
            
            if (!beds || beds.length === 0) { 
                document.getElementById('bedSelect').innerHTML = '<option value="">No available beds in this ward</option>'; 
                return; 
            }
            
            document.getElementById('bedSelect').innerHTML = '<option value="">-- Select Bed --</option>' + beds.map(b => `<option value="${b.bed_id}">Bed ${b.bed_number} (${b.bed_type || 'Standard'})</option>`).join('');
        } catch (error) { 
            console.error('Error loading beds:', error);
            document.getElementById('bedSelect').innerHTML = '<option value="">Error loading beds</option>';
        }
    }

    async function admitPatient() {
        const patientId = document.getElementById('assignPatientSelect').value;
        const wardId = document.getElementById('wardSelect').value;
        const bedId = document.getElementById('bedSelect').value;
        const primaryDiagnosis = document.getElementById('primaryDiagnosis').value;
        
        if (!patientId || !wardId || !bedId) { 
            alert('Please select patient, ward, and bed'); 
            return; 
        }
        
        try {
            const response = await fetch('/admissions', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                body: JSON.stringify({
                    patient_id: parseInt(patientId),
                    bed_id: parseInt(bedId),
                    ward_id: parseInt(wardId),
                    primary_diagnosis: primaryDiagnosis
                })
            });
            
            const result = await response.json();
            
            if (result.success) { 
                alert('✅ Patient admitted successfully!');
                document.getElementById('primaryDiagnosis').value = '';
                fetchStats(); 
                loadActiveAdmissions();
                document.getElementById('assignPatientSelect').value = '';
                document.getElementById('wardSelect').value = '';
                document.getElementById('bedSelect').innerHTML = '<option value="">-- First select a ward --</option>';
            } else { 
                alert('❌ Error: ' + (result.message || 'Admission failed')); 
            }
        } catch (error) { 
            console.error('Error:', error);
            alert('❌ Error admitting patient'); 
        }
    }

    async function loadActiveAdmissions() {
        try {
            // Get active admissions directly from Supabase
            const response = await fetch(`${supabaseUrl}/rest/v1/in_patient?actual_leave=is.null&select=*,patient(first_name,last_name),bed(bed_number),ward(ward_name)`, {
                headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
            });
            const admissions = await response.json();
            
            const container = document.getElementById('activeAdmissionsList');
            if (!admissions || admissions.length === 0) { 
                container.innerHTML = '<div class="text-center py-8">No active admissions</div>'; 
                return; 
            }
            
            container.innerHTML = admissions.map(adm => {
                const patient = adm.patient || {};
                const bed = adm.bed || {};
                const ward = adm.ward || {};
                return `
                    <div class="record-card">
                        <div style="display: flex; justify-content: space-between;">
                            <strong>${patient.first_name || 'Unknown'} ${patient.last_name || ''}</strong>
                            <button onclick="dischargePatient(${adm.inpatient_id})" class="table-action-btn" style="background: #dc2626; color: white;">Discharge</button>
                        </div>
                        <div style="font-size: 0.75rem; margin-top: 8px;">
                            <div>🛏️ Bed: ${bed.bed_number || 'N/A'}</div>
                            <div>🏥 Ward: ${ward.ward_name || 'Ward ' + adm.ward_id}</div>
                            <div>📅 Admitted: ${new Date(adm.date_admitted).toLocaleDateString()}</div>
                            <div>🩺 ${adm.primary_diagnosis || 'No diagnosis'}</div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (error) { 
            console.error('Error loading admissions:', error);
            document.getElementById('activeAdmissionsList').innerHTML = '<div class="text-center py-8 text-danger">Error loading admissions</div>';
        }
    }

    async function dischargePatient(inpatientId) {
        if (!confirm('Discharge this patient?')) return;
        
        try {
            const response = await fetch(`/admissions/${inpatientId}/discharge`, { 
                method: 'PUT', 
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify({
                    discharge_date: new Date().toISOString().split('T')[0]
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ Patient discharged successfully');
                fetchStats();
                loadActiveAdmissions();
                loadPatients();
            } else {
                alert('❌ Error: ' + (result.message || 'Discharge failed'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error discharging patient');
        }
    }

    function showView(view) {
        document.getElementById('registrationView').style.display = 'none';
        document.getElementById('patientsView').style.display = 'none';
        document.getElementById('recordsView').style.display = 'none';
        document.getElementById('admissionsView').style.display = 'none';
        document.getElementById(`${view}View`).style.display = 'block';
        
        const btnPrimary = 'btn-primary-custom', btnSecondary = 'btn-secondary-custom';
        document.getElementById('btnRegistration').className = view === 'registration' ? btnPrimary : btnSecondary;
        document.getElementById('btnPatients').className = view === 'patients' ? btnPrimary : btnSecondary;
        document.getElementById('btnRecords').className = view === 'records' ? btnPrimary : btnSecondary;
        document.getElementById('btnAdmissions').className = view === 'admissions' ? btnPrimary : btnSecondary;
        
        if (view === 'patients') loadPatients();
        if (view === 'records') { loadMedicalRecords(); loadPatientSelects(); }
        if (view === 'admissions') { loadActiveAdmissions(); loadPatientSelects(); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchStats(); 
        loadPatients(); 
        loadMedicalRecords(); 
        loadPatientSelects();
        loadActiveAdmissions();
        showView('registration');
    });
</script>
@endsection