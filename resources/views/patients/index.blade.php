<x-app-layout>
    <div class="dashboard-wrapper">
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
        <div id="registrationView" class="view-panel" style="display: block;">
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Patient Registration Form
                </div>
                <div class="card-body">
                    <form id="registrationForm" onsubmit="registerPatient(event)">
                        @csrf
                        <div class="form-grid">
                            <div>
                                <label class="form-label">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Gender</label>
                                <select id="sex" name="sex" class="form-input">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Marital Status</label>
                                <select id="marital_status" name="marital_status" class="form-input">
                                    <option value="">Select</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                            <div class="full-width">
                                <label class="form-label">Address</label>
                                <textarea id="address" name="address" rows="2" class="form-input"></textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn-primary-custom">
                                <i class="fas fa-save"></i> Register Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- VIEW 2: PATIENTS LIST -->
        <div id="patientsView" class="view-panel" style="display: none;">
            <div class="table-card">
                <div class="card-header">
                    <i class="fas fa-users"></i> Registered Patients List
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 10px; margin-bottom: 16px;">
                        <input type="text" id="searchInput" placeholder="🔍 Search by name, ID, or phone..."
                            class="form-input" style="flex: 1;">
                        <button onclick="loadPatients()" class="btn-primary-custom">Search</button>
                        <button onclick="clearSearch()" class="btn-secondary-custom">Clear</button>
                    </div>
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
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="recordsList">
                        <div class="text-center py-8">Loading medical records...</div>
                    </div>
                </div>
                <div class="form-card">
                    <div class="card-header"><i class="fas fa-plus-circle"></i> Add New Medical Record</div>
                    <div class="card-body">
                        <select id="recordPatientSelect" class="form-input" style="width:100%; margin-bottom:12px;">
                            <option value="">-- Select Patient --</option>
                        </select>
                        <input type="text" id="diagnosis" class="form-input" placeholder="Diagnosis"
                            style="width:100%; margin-bottom:12px;">
                        <select id="bloodType" class="form-input" style="width:100%; margin-bottom:12px;">
                            <option value="">Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                        <textarea id="allergiesRecord" rows="2" class="form-input" placeholder="Allergies"
                            style="width:100%; margin-bottom:12px;"></textarea>
                        <textarea id="chronic_conditions" rows="2" class="form-input" placeholder="Chronic Conditions"
                            style="width:100%; margin-bottom:12px;"></textarea>
                        <button onclick="saveMedicalRecord()" class="btn-primary-custom w-100">Save Medical Record</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW 4: ADMISSIONS & BEDS - FIXED VERSION -->
        <div id="admissionsView" class="view-panel" style="display: none;">
            <div class="grid-2">
                <div class="table-card">
                    <div class="card-header"><i class="fas fa-procedures"></i> Active Admissions</div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="activeAdmissionsList">
                        <div class="text-center py-8">Loading active admissions...</div>
                    </div>
                </div>
                <div class="form-card">
                    <div class="card-header"><i class="fas fa-bed"></i> Assign Patient to Ward & Bed</div>
                    <div class="card-body">
                        <label class="form-label">Select Patient *</label>
                        <select id="assignPatientSelect" class="form-input" style="width:100%; margin-bottom:12px;" onchange="updateSelectedPatientInfo()">
                            <option value="">-- Select Patient --</option>
                        </select>
                        
                        <label class="form-label">Select Ward *</label>
                        <select id="wardSelect" class="form-input" onchange="loadAvailableBeds()" style="width:100%; margin-bottom:12px;">
                            <option value="">-- Select Ward --</option>
                        </select>
                        
                        <label class="form-label">Select Bed *</label>
                        <select id="bedSelect" class="form-input" style="width:100%; margin-bottom:12px;">
                            <option value="">-- First select a ward --</option>
                        </select>
                        
                        <label class="form-label">Primary Diagnosis *</label>
                        <textarea id="primaryDiagnosis" rows="3" class="form-input" placeholder="Enter primary diagnosis..." style="width:100%; margin-bottom:16px;"></textarea>
                        
                        <label class="form-label">Condition</label>
                        <select id="patientCondition" class="form-input" style="width:100%; margin-bottom:16px;">
                            <option value="Stable">Stable</option>
                            <option value="Critical">Critical</option>
                            <option value="Serious">Serious</option>
                            <option value="Fair">Fair</option>
                            <option value="Good">Good</option>
                        </select>
                        
                        <div id="selectedPatientInfo" style="background: #f0f9ff; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; display: none;">
                            <strong>Selected Patient:</strong> <span id="selectedPatientName"></span><br>
                            <strong>Patient ID:</strong> <span id="selectedPatientId"></span>
                        </div>
                        
                        <button onclick="admitPatient(event)" class="btn-primary-custom w-100">
                            <i class="fas fa-procedures"></i> Admit Patient
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* DASHBOARD WRAPPER - MAIN MARGINS ADDED */
        .dashboard-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 28px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.2s ease;
            border: 1px solid #eef2f8;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-card.total {
            border-left: 4px solid #83D475;
        }

        .stat-card.admissions {
            border-left: 4px solid #3b82f6;
        }

        .stat-card.beds {
            border-left: 4px solid #f59e0b;
        }

        .stat-card.records {
            border-left: 4px solid #8b5cf6;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .btn-primary-custom {
            background-color: #83D475;
            color: white;
        }

        .btn-primary-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary-custom:hover {
            background-color: #d1d5db;
            transform: translateY(-1px);
        }

        .form-card,
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #eef2f8;
        }

        .card-header {
            padding: 16px 24px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 18px;
        }

        .card-body {
            padding: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #83D475;
            box-shadow: 0 0 0 3px rgba(131, 212, 117, 0.1);
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table th,
        .custom-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .custom-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .custom-table tr:hover {
            background: #fafbfc;
        }

        .table-action-btn {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            background: #e5e7eb;
            border: none;
            margin-right: 6px;
            transition: all 0.2s;
        }

        .table-action-btn:hover {
            background: #d1d5db;
        }

        .table-action-btn.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .table-action-btn.danger:hover {
            background: #fecaca;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 28px;
        }

        .record-card {
            background: #f9fafb;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            border: 1px solid #eef2f8;
            transition: all 0.2s;
        }

        .record-card:hover {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .text-center {
            text-align: center;
        }

        .py-8 {
            padding: 32px 0;
        }

        .mt-4 {
            margin-top: 20px;
        }

        .w-100 {
            width: 100%;
        }

        .overflow-x-auto {
            overflow-x: auto;
        }

        .text-red-500 {
            color: #ef4444;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-wrapper {
                padding: 20px 16px;
            }
            
            .grid-2 {
                gap: 20px;
            }
            
            .card-body {
                padding: 16px;
            }
            
            .stats-grid {
                gap: 12px;
            }
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const supabaseUrl = '{{ env("SUPABASE_URL") }}';
        const supabaseKey = '{{ env("SUPABASE_KEY") }}';

        function escapeHtml(str) { if (!str) return ''; return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;'); }
        function clearSearch() { document.getElementById('searchInput').value = ''; loadPatients(); }

        async function fetchStats() {
            try {
                const patientsRes = await fetch('/stats/total-patients');
                const patientsData = await patientsRes.json();
                document.getElementById('totalPatients').textContent = patientsData.count || 0;
                const admissionsRes = await fetch('/stats/active-admissions');
                const admissionsData = await admissionsRes.json();
                document.getElementById('activeAdmissions').textContent = admissionsData.count || 0;
                const bedsRes = await fetch('/stats/occupied-beds');
                const bedsData = await bedsRes.json();
                document.getElementById('occupiedBeds').textContent = bedsData.count || 0;
                const recordsRes = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=record_id`, {
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                const recordsData = await recordsRes.json();
                document.getElementById('medicalRecords').textContent = recordsData?.length || 0;
            } catch (error) { console.error('Error fetching stats:', error); }
        }

        async function loadPatients() {
            try {
                const search = document.getElementById('searchInput')?.value || '';
                const response = await fetch(`/patients/list?search=${encodeURIComponent(search)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const patients = await response.json();
                const tbody = document.getElementById('patientsTableBody');
                if (!patients || patients.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8">No patients found.</td></tr>'; return; }
                tbody.innerHTML = patients.map(p => {
                    let age = 'N/A';
                    if (p.dob) { const birthDate = new Date(p.dob); const today = new Date(); let a = today.getFullYear() - birthDate.getFullYear(); const m = today.getMonth() - birthDate.getMonth(); if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) a--; age = a; }
                    return `<tr><td>${p.patient_id}</td><td><strong>${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}</strong></td><td>${age}</td><td>${escapeHtml(p.sex) || '—'}</td><td>${escapeHtml(p.phone) || '—'}</td><td>${escapeHtml(p.marital_status) || '—'}</td><td><button onclick="viewPatient(${p.patient_id})" class="table-action-btn">View</button><button onclick="deletePatient(${p.patient_id})" class="table-action-btn danger">Delete</button></td></tr>`;
                }).join('');
            } catch (error) { console.error('Error loading patients:', error); document.getElementById('patientsTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Error loading patients</td></tr>'; }
        }

        async function loadPatientSelects() {
            try {
                console.log('Loading patient selects...');
                
                const allResponse = await fetch('/patients/list', {
                    headers: { 'Accept': 'application/json' }
                });

                if (!allResponse.ok) {
                    throw new Error(`HTTP ${allResponse.status}`);
                }

                const allPatients = await allResponse.json();
                console.log('Total patients loaded:', allPatients.length);

                const detailsResponse = await fetch('/stats/active-admissions/details');
                let admittedDetails = [];
                try {
                    admittedDetails = await detailsResponse.json();
                } catch(e) {
                    console.log('No active admissions yet');
                }

                const admittedIds = new Set();
                if (admittedDetails && admittedDetails.length) {
                    admittedDetails.forEach(adm => {
                        if (adm.patient_id) admittedIds.add(parseInt(adm.patient_id));
                    });
                }
                console.log('Admitted patient IDs:', Array.from(admittedIds));

                const nonAdmittedPatients = allPatients.filter(p => !admittedIds.has(p.patient_id));
                console.log('Non-admitted patients:', nonAdmittedPatients.length);

                const admissionsOptions = '<option value="">-- Select Patient --</option>' +
                    nonAdmittedPatients.map(p => `<option value="${p.patient_id}" data-name="${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)} (ID: ${p.patient_id})</option>`).join('');

                const medicalRecordsResponse = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=patient_id`, {
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                const medicalRecordsData = await medicalRecordsResponse.json();
                const recordsArray = Array.isArray(medicalRecordsData) ? medicalRecordsData : (medicalRecordsData.data || []);
                const patientsWithRecords = new Set(recordsArray.map(r => r.patient_id));

                const patientsWithoutRecords = allPatients.filter(p => !patientsWithRecords.has(p.patient_id));
                const recordsOptions = '<option value="">-- Select Patient --</option>' +
                    patientsWithoutRecords.map(p => `<option value="${p.patient_id}">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)} (ID: ${p.patient_id})</option>`).join('');

                if (document.getElementById('recordPatientSelect')) {
                    document.getElementById('recordPatientSelect').innerHTML = recordsOptions;
                }
                if (document.getElementById('assignPatientSelect')) {
                    document.getElementById('assignPatientSelect').innerHTML = admissionsOptions;
                }

                if (nonAdmittedPatients.length === 0) {
                    const assignSelect = document.getElementById('assignPatientSelect');
                    if (assignSelect && assignSelect.options.length === 1) {
                        assignSelect.innerHTML = '<option value="">-- No available patients (all admitted) --</option>';
                    }
                }

            } catch (error) {
                console.error('Error loading patient selects:', error);
                try {
                    const response = await fetch('/patients/list', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const patients = await response.json();
                    const options = '<option value="">-- Select Patient --</option>' +
                        patients.map(p => `<option value="${p.patient_id}">${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)} (ID: ${p.patient_id})</option>`).join('');

                    if (document.getElementById('recordPatientSelect')) {
                        document.getElementById('recordPatientSelect').innerHTML = options;
                    }
                    if (document.getElementById('assignPatientSelect')) {
                        document.getElementById('assignPatientSelect').innerHTML = options;
                    }
                } catch (fallbackError) {
                    console.error('Fallback also failed:', fallbackError);
                }
            }
        }

        function updateSelectedPatientInfo() {
            const select = document.getElementById('assignPatientSelect');
            const selectedOption = select.options[select.selectedIndex];
            const patientInfoDiv = document.getElementById('selectedPatientInfo');
            
            if (select.value && selectedOption && selectedOption.dataset.name) {
                document.getElementById('selectedPatientName').innerText = selectedOption.dataset.name;
                document.getElementById('selectedPatientId').innerText = select.value;
                patientInfoDiv.style.display = 'block';
            } else if (select.value) {
                document.getElementById('selectedPatientName').innerText = selectedOption.text.split('(')[0].trim();
                document.getElementById('selectedPatientId').innerText = select.value;
                patientInfoDiv.style.display = 'block';
            } else {
                patientInfoDiv.style.display = 'none';
            }
        }

        async function loadWards() {
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/ward?select=ward_id,ward_name&order=ward_name.asc`, {
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                const wards = await response.json();
                const wardSelect = document.getElementById('wardSelect');
                if (wardSelect) wardSelect.innerHTML = '<option value="">-- Select Ward --</option>' + wards.map(w => `<option value="${w.ward_id}">${escapeHtml(w.ward_name)}</option>`).join('');
            } catch (error) { console.error('Error loading wards:', error); }
        }

        async function loadAvailableBeds() {
            const wardId = document.getElementById('wardSelect').value;
            if (!wardId) { document.getElementById('bedSelect').innerHTML = '<option value="">-- First select a ward --</option>'; return; }
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/bed?ward_id=eq.${wardId}&is_available=eq.true&select=bed_id,bed_number,bed_type`, {
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                const beds = await response.json();
                if (!beds || beds.length === 0) { document.getElementById('bedSelect').innerHTML = '<option value="">No available beds in this ward</option>'; return; }
                document.getElementById('bedSelect').innerHTML = '<option value="">-- Select Bed --</option>' + beds.map(b => `<option value="${b.bed_id}">Bed ${b.bed_number} (${b.bed_type || 'Standard'})</option>`).join('');
            } catch (error) { console.error('Error loading beds:', error); }
        }

        async function registerPatient(event) {
            event.preventDefault();
            const formData = {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                dob: document.getElementById('dob').value || null,
                sex: document.getElementById('sex').value || null,
                phone: document.getElementById('phone').value || null,
                marital_status: document.getElementById('marital_status').value || null,
                address: document.getElementById('address').value || null,
                date_registered: new Date().toISOString().split('T')[0]
            };
            try {
                const response = await fetch('/patients', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify(formData) });
                const result = await response.json();
                if (result.success || result.patient_id) {
                    alert('✅ Patient registered successfully!');
                    document.getElementById('registrationForm').reset();
                    fetchStats(); loadPatients(); loadPatientSelects(); showView('patients');
                } else { alert('❌ Error: ' + (result.message || 'Registration failed')); }
            } catch (error) { console.error('Error:', error); alert('❌ Error registering patient: ' + error.message); }
        }

        async function admitPatient(event) {
            const patientId = document.getElementById('assignPatientSelect').value;
            const wardId = document.getElementById('wardSelect').value;
            const bedId = document.getElementById('bedSelect').value;
            const primaryDiagnosis = document.getElementById('primaryDiagnosis').value;
            const condition = document.getElementById('patientCondition') ? document.getElementById('patientCondition').value : 'Stable';

            if (!patientId) {
                alert('❌ Please select a patient');
                return;
            }
            if (!wardId) {
                alert('❌ Please select a ward');
                return;
            }
            if (!bedId) {
                alert('❌ Please select a bed');
                return;
            }
            if (!primaryDiagnosis) {
                alert('❌ Please enter a primary diagnosis');
                return;
            }

            const admitBtn = event ? event.target : document.querySelector('#admissionsView .btn-primary-custom');
            const originalText = admitBtn ? admitBtn.innerHTML : 'Admit Patient';
            if (admitBtn) {
                admitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Admitting...';
                admitBtn.disabled = true;
            }

            try {
                const response = await fetch('/admissions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_id: parseInt(patientId),
                        bed_id: parseInt(bedId),
                        ward_id: parseInt(wardId),
                        diagnosis: primaryDiagnosis,
                        condition: condition
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('✅ Patient admitted successfully!');
                    document.getElementById('primaryDiagnosis').value = '';
                    if (document.getElementById('patientCondition')) {
                        document.getElementById('patientCondition').value = 'Stable';
                    }
                    document.getElementById('assignPatientSelect').value = '';
                    document.getElementById('wardSelect').value = '';
                    document.getElementById('bedSelect').innerHTML = '<option value="">-- First select a ward --</option>';
                    document.getElementById('selectedPatientInfo').style.display = 'none';
                    
                    await fetchStats();
                    await loadActiveAdmissions();
                    await loadPatientSelects();
                    await loadPatients();
                } else {
                    alert('❌ Error: ' + (result.message || 'Admission failed'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error admitting patient: ' + error.message);
            } finally {
                if (admitBtn) {
                    admitBtn.innerHTML = originalText;
                    admitBtn.disabled = false;
                }
            }
        }

        async function loadActiveAdmissions() {
            try {
                console.log('Loading active admissions...');
                const response = await fetch('/stats/active-admissions/details');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const admissions = await response.json();
                console.log('Admissions loaded:', admissions.length);
                
                const container = document.getElementById('activeAdmissionsList');
                if (!admissions || admissions.length === 0) { 
                    container.innerHTML = '<div class="text-center py-8">No active admissions</div>'; 
                    return; 
                }
                
                container.innerHTML = admissions.map(adm => `
                    <div class="record-card">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <strong style="font-size: 16px;">${escapeHtml(adm.patient_name) || 'Unknown'}</strong>
                                <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">ID: ${adm.patient_id || 'N/A'}</div>
                            </div>
                            <button onclick="dischargePatient(${adm.inpatient_id})" class="table-action-btn" style="background: #dc2626; color: white; padding: 4px 12px;">
                                Discharge
                            </button>
                        </div>
                        <div style="font-size: 13px; margin-top: 10px;">
                            <div>🛏️ Bed: ${escapeHtml(adm.bed_number) || 'N/A'}</div>
                            <div>🏥 Ward: ${escapeHtml(adm.ward_name) || 'N/A'}</div>
                            <div>📅 Admitted: ${adm.date_admitted ? new Date(adm.date_admitted).toLocaleDateString() : 'N/A'}</div>
                            <div>🩺 Diagnosis: ${escapeHtml(adm.primary_diagnosis) || 'No diagnosis'}</div>
                        </div>
                    </div>
                `).join('');
            } catch (error) { 
                console.error('Error loading admissions:', error); 
                document.getElementById('activeAdmissionsList').innerHTML = '<div class="text-center py-8 text-red-500">Error loading active admissions</div>'; 
            }
        }

        async function dischargePatient(inpatientId) {
            if (!confirm('Discharge this patient?')) return;
            try {
                const response = await fetch(`/admissions/${inpatientId}/discharge`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ discharge_date: new Date().toISOString().split('T')[0] }) });
                const result = await response.json();
                if (result.success) { alert('✅ Patient discharged successfully'); fetchStats(); loadActiveAdmissions(); loadPatients(); loadPatientSelects(); }
                else { alert('❌ Error: ' + (result.message || 'Discharge failed')); }
            } catch (error) { console.error('Error:', error); alert('Error discharging patient'); }
        }

        async function loadMedicalRecords() {
            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?select=*,patient:patient_id(first_name,last_name)&order=created_date.desc`, {
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                const records = await response.json();
                const container = document.getElementById('recordsList');
                if (!records || records.length === 0) { container.innerHTML = '<div class="text-center py-8">No medical records found.</div>'; return; }
                container.innerHTML = records.map(record => { const patient = record.patient || {}; return `<div class="record-card"><div style="display: flex; justify-content: space-between;"><strong>${escapeHtml(patient.first_name) || 'Unknown'} ${escapeHtml(patient.last_name) || ''}</strong><button onclick="deleteMedicalRecord(${record.record_id})" class="table-action-btn danger">Delete</button></div><div><strong>🩸 Blood Type:</strong> ${escapeHtml(record.blood_type) || 'Not recorded'}</div><div><strong>⚠️ Allergies:</strong> ${escapeHtml(record.allergies) || 'None'}</div><div><strong>📋 Diagnosis:</strong> ${escapeHtml(record.diagnosis) || 'Not recorded'}</div><div><strong>📅 Date:</strong> ${record.created_date || new Date(record.created_at).toLocaleDateString()}</div></div>`; }).join('');
            } catch (error) { console.error('Error loading medical records:', error); document.getElementById('recordsList').innerHTML = '<div class="text-center py-8 text-red-500">Error loading medical records</div>'; }
        }

        async function saveMedicalRecord() {
            const patientId = document.getElementById('recordPatientSelect').value;
            if (!patientId) { alert('Please select a patient'); return; }

            const formData = {
                patient_id: parseInt(patientId),
                diagnosis: document.getElementById('diagnosis').value,
                blood_type: document.getElementById('bloodType').value,
                allergies: document.getElementById('allergiesRecord').value,
                chronic_conditions: document.getElementById('chronic_conditions').value,
                created_date: new Date().toISOString().split('T')[0],
                created_at: new Date().toISOString()
            };

            try {
                const response = await fetch(`${supabaseUrl}/rest/v1/patient_medical_record`, {
                    method: 'POST',
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    alert('✅ Medical record saved!');
                    document.getElementById('diagnosis').value = '';
                    document.getElementById('bloodType').value = '';
                    document.getElementById('allergiesRecord').value = '';
                    document.getElementById('chronic_conditions').value = '';
                    loadMedicalRecords();
                    fetchStats();
                    loadPatientSelects();
                } else {
                    alert('❌ Error saving record');
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        }

        async function deleteMedicalRecord(recordId) {
            if (!confirm('Delete this record?')) return;
            try {
                await fetch(`${supabaseUrl}/rest/v1/patient_medical_record?record_id=eq.${recordId}`, {
                    method: 'DELETE',
                    headers: { 'apikey': supabaseKey, 'Authorization': `Bearer ${supabaseKey}` }
                });
                alert('✅ Record deleted');
                loadMedicalRecords();
                fetchStats();
                loadPatientSelects();
            } catch (error) {
                alert('Error deleting record');
            }
        }

        async function deletePatient(patientId) {
            if (!confirm('Delete this patient?')) return;
            try { const response = await fetch(`/patients/${patientId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' } }); const result = await response.json(); if (result.success) { alert('✅ Patient deleted'); fetchStats(); loadPatients(); loadPatientSelects(); } else { alert('Error: ' + (result.message || 'Delete failed')); } }
            catch (error) { alert('Error deleting patient'); }
        }

        function viewPatient(patientId) { window.location.href = `/patients/${patientId}`; }

        function showView(view) {
            document.getElementById('registrationView').style.display = view === 'registration' ? 'block' : 'none';
            document.getElementById('patientsView').style.display = view === 'patients' ? 'block' : 'none';
            document.getElementById('recordsView').style.display = view === 'records' ? 'block' : 'none';
            document.getElementById('admissionsView').style.display = view === 'admissions' ? 'block' : 'none';
            const btnPrimary = 'btn-primary-custom', btnSecondary = 'btn-secondary-custom';
            document.getElementById('btnRegistration').className = view === 'registration' ? btnPrimary : btnSecondary;
            document.getElementById('btnPatients').className = view === 'patients' ? btnPrimary : btnSecondary;
            document.getElementById('btnRecords').className = view === 'records' ? btnPrimary : btnSecondary;
            document.getElementById('btnAdmissions').className = view === 'admissions' ? btnPrimary : btnSecondary;
            if (view === 'patients') loadPatients();
            if (view === 'records') { loadMedicalRecords(); loadPatientSelects(); }
            if (view === 'admissions') { loadActiveAdmissions(); loadPatientSelects(); }
        }

        document.addEventListener('DOMContentLoaded', function () {
            fetchStats(); loadPatients(); loadMedicalRecords(); loadPatientSelects(); loadWards(); loadActiveAdmissions(); showView('registration');
        });
    </script>
</x-app-layout>